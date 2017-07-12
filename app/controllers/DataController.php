<?php

use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class DataController extends ControllerBase
{


    public function indexAction()
    {
    	return $this->dispatcher->forward(array(
                        "controller" => "data",
                        "action" => "listtabledata"
        ));
    }

    //this method list all tables alongwith their coloumns with their data types and validations
    public function listtabledataAction(){

        $db_name = $this->config->database['dbname'];

        $query = "SELECT TABLE_NAME FROM `TABLES` WHERE `TABLE_SCHEMA` = '".$db_name."'";
        $tables = new Resultset(null, null, $this->formDb->query($query, null));
        $tables = $tables->toArray();
        foreach ($tables as  $table) {
        	$cquery = "SELECT * from COLUMNS WHERE TABLE_SCHEMA = '".$db_name."' AND 
                       TABLE_NAME = '".$table['TABLE_NAME']."'";
        	$columns = new Resultset(null, null, $this->formDb->query($cquery, null));
        	$columns = $columns->toArray();

        	foreach ($columns as $key=>$column) {
        		foreach ($column as $key => $columnData) {
        			$tabledata[$table['TABLE_NAME']][$column['COLUMN_NAME']][$key] = $columnData;
           		}
        	}
        }
        // echo "<pre>";
        // print_r($tabledata); die;
        $this->view->tablecolumndata = $tabledata;
    }

    //this will generate necessary code in controllers and views folder
    public function generatecodeAction(){
        if (!$this->request->isPost() || empty($this->request->getPost())) {
            return $this->dispatcher->forward(array(
                        "controller" => "data",
                        "action" => "listtabledata"
            ));
        }
        echo "<pre>";
        $tables = $this->request->getPost('checkbox_table');
        foreach ($tables as $table) {
            $controller_name    =   $this->request->getPost('controller_'.$table);
            $action_name        =   $this->request->getPost('action_'.$table);
            $formString         =   '';
            
            if($controller_name != 'Data'){
                $table_columns = $this->request->getPost($table);
                if($table_columns){
                    clearstatcache();

                    if (!file_exists(BASE_PATH.'/app/controllers/'.$controller_name.'Controller.php')){
                        $this->createNewFile($controller_name.'Controller', $action_name.'Action', $table);
                    }else{
                        $this->createOnlyMethod($controller_name.'Controller', $action_name.'Action', $table);
                    }
                    $modelName = implode('', array_map('ucfirst',explode('_', $table)));
                    if(!file_exists(BASE_PATH.'/app/models/'.$modelName.'.php')) {
                        $this->createModel($table);
                    }
                }else{
                    echo "you did't select any column for table ".$table;
                }
                foreach($table_columns as $table_column){
                    echo $table_column.' is of type '.$this->request->getPost('input_type_'.$table.'_'.$table_column)."<br>";

                    $formString .= $this->formString($table, $table_column, $this->request->getPost());
                }

                if($formString != ''){
                    $this->createView($controller_name, $action_name, $formString, $table);
                }
            
            }else{
            echo "Controller name must Be Different from Data for table ".$table;
            }
        }
        die;
    }


    private function createView($controllerName, $actionName, $formString, $table){

        $html_string = '<h2>'.ucwords(str_replace('_', ' ', $table)).'</h2>
                        <?php 
                            $this->flashSession->output();   
                            echo $this->tag->form(array("'.strtolower($controllerName).'/'.strtolower($actionName).'", "class"=>"form-horizontal", "role"=>"form"));
                        ?>
                        ';
        $html_string .= $formString;
        $html_string .= '<div class="form-group">        
                            <div class="col-sm-offset-2 col-sm-4">
                              <button type="submit" class="btn btn-default">Submit</button>
                            </div>
                          </div>
                          </form>';

        $view = BASE_PATH.'/app/views/'.strtolower($controllerName).'/'.$actionName.'.phtml';
        $view_folder = BASE_PATH.'/app/views/'.strtolower($controllerName);
        if(!file_exists($view_folder)){
            mkdir($view_folder, 0777, true);
        }

        $viewfile = fopen($view, "w") or die("Unable to open file!");
        fwrite($viewfile, $html_string);
        fclose($viewfile);
    } 
    
    private function formString($tableName, $columnName, $columnData){

        if(!in_array($columnData['input_type_'.$tableName.'_'.$columnName],array("textarea", "select", "select_multiple")))
        {   
            $input_type = $columnData['input_type_'.$tableName.'_'.$columnName];
            $required = $columnData['required_'.$tableName.'_'.$columnName];
            $html_string = '<div class="form-group">
                                    <label class="control-label col-sm-2" for="'.$columnName.'">'.ucwords(str_replace('_', ' ', $columnName)).':</label>
                                    <div class="col-sm-4">
                                      <input type="'.$input_type.'" class="form-control" id="'.$columnName.'" placeholder="Enter '.ucwords(str_replace('_', ' ', $columnName)).'" name="'.$columnName.'" maxlength="" '.(($required == 'yes')?'required="required"':'').'>
                                    </div>
                                  </div>
                                  ';
        }else if($columnData['input_type_'.$tableName.'_'.$columnName] == 'textarea'){
            $required = $columnData['required_'.$tableName.'_'.$columnName];
            $html_string = '<div class="form-group">
                                    <label class="control-label col-sm-2" for="'.$columnName.'">'.ucwords(str_replace('_', ' ', $columnName)).':</label>
                                    <div class="col-sm-4">
                                      <textarea class="form-control" rows="5" id="'.$columnName.'" name="'.$columnName.'" '.(($required == 'yes')?'required="required"':'').'>
                                      </textarea>
                                    </div>
                                  </div>
                                  ';
        }else if(in_array($columnData['input_type_'.$tableName.'_'.$columnName],array("select", "select_multiple"))) {
            $input_type = $columnData['input_type_'.$tableName.'_'.$columnName];
            $required = $columnData['required_'.$tableName.'_'.$columnName];
            $html_string = '<div class="form-group">
                                    <label class="control-label col-sm-2" for="'.$columnName.'">'.ucwords(str_replace('_', ' ', $columnName)).':</label>
                                    <div class="col-sm-4">
                                      <select class="form-control" id="'.$columnName.'" name="'.$columnName.'" '.(($required == 'yes')?'required="required"':'').' '.(($input_type == 'select_multiple')?'multiple':'').'>';
                                    if(isset($required)){  
                                        $html_string.= '<option value="">Select Option</option>';
                                    }
                                    if(isset($columnData['enum_set_vals_'.$tableName.'_'.$columnName])){
                                        foreach ($columnData['enum_set_vals_'.$tableName.'_'.$columnName] as $value) {
                                          $html_string .= '<option value="'.$value.'">'.$value.'</option>';
                                        }
                                    }else{
                                        $html_string.=  '<option value="">Sample Value 2</option>
                                                         <option value="">Sample Value 2</option>';
                                    }
                $html_string.=        '</select>
                                    </div>
                                  </div>
                                  ';
        }
        return $html_string;
    }

    //creating new controller first time
    private function createNewFile($controllerName, $actionName, $tableName){
        $file = BASE_PATH.'/app/controllers/'.$controllerName.'.php';
        $modelName = implode('', array_map('ucfirst',explode('_', $tableName)));
        $dataString =  '<?php
class '.$controllerName.' extends ControllerBase
{
    public function indexAction()
    {
        // write code here;  
    }
    public function '.$actionName.'(){
        if($this->request->isPost()){
            $modelObj = new '.$modelName.'();
            $postData = $this->request->getPost();
            foreach ($postData as $key => $data) {
                $modelObj->$key = $data;
            }
            if($modelObj->save()){
                $this->flashSession->success(" Data Saved Successfully");
            }
        }
    }
}';
        $newfile = fopen($file, "w") or die("Unable to open file!");
        fwrite($newfile, $dataString);
        fclose($newfile);

    }

    // append method in existing controller file
    private function createOnlyMethod($controllerName, $actionName, $tableName){
        $file = BASE_PATH.'/app/controllers/'.$controllerName.'.php';
        $modelName = implode('', array_map('ucfirst',explode('_', $tableName)));
        $dataString =  '
    public function '.$actionName.'(){
        if($this->request->isPost()){
            $modelObj = new '.$modelName.'();
            $postData = $this->request->getPost();
            foreach ($postData as $key => $data) {
                $modelObj->$key = $data;
            }
            if($modelObj->save()){
                $this->flashSession->success(" Data Saved Successfully");
            }
        }
    }';
       $this->appendMethod($file, $dataString);
    }


    private function createModel($tableName){
        $modelName = implode('', array_map('ucfirst',explode('_', $tableName)));
        $file = BASE_PATH.'/app/models/'.$modelName.'.php';
        $dataString =  '<?php
class '.$modelName.' extends \Phalcon\Mvc\Model
{
    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return "'.$tableName.'";
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}';
        $newfile = fopen($file, "w") or die("Unable to open file!");
        fwrite($newfile, $dataString);
        fclose($newfile);
    }

    //function for appending string before last non-blank line
    private function appendMethod($file, $dataString){
        $filecontent = file_get_contents($file); 
        $str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $filecontent);
        file_put_contents($file, $str);

        $fc = fopen($file, "r");
        while (!feof($fc)) {
            $buffer = fgets($fc, 4096);
            $lines[] = $buffer;
        }

        fclose($fc);

        //open same file and use "w" to clear file 
        $f = fopen($file, "w") or die("couldn't open $file");

        $lineCount = count($lines);
        //loop through array writing the lines until the secondlast
        for ($i = 0; $i < $lineCount- 2; $i++) {
            fwrite($f, $lines[$i]);
        }
        fwrite($f, $dataString.PHP_EOL);

        //write the last line
        fwrite($f, $lines[$lineCount-2]);
        fclose($f);
    }
}