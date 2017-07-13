# phalcon_codegenmysql
Phalcon for Automatically generating controllers, models and views for Mysql Databases

We can use this for auto generating the code for mysql database tables.

e.g Controllers , Models And Views

Guidelines for using this as follows:-

1. First You need to Install PHP, MySql and a web server e.g. apache2, nginx on your machine.
	On Debian/Ubuntu
	i. 	apt-get install tasksel
	ii.	now run command tasksel  and choose LAMP server and follow instructions

2. First We need to install the latest version of phalcon on our machine

	Ubuntu Installation:-

	https://syntaxsugar.github.io/posts/2015/09/how-to-install-phalcon-on-ubuntu-1404-lts/

	Windows Installation:-

	https://olddocs.phalconphp.com/en/3.0.3/reference/install.html


2. Now clone phalcon_codegenmysql repo from github on your webserver directory using link:-

	https://github.com/jitender3222/phalcon_codegenmysql.git

3. Now go to config file i.e. phalcon_codegenmysql/app/config/config.php  and edit following details
			
			#configure username and password to use code gen
			defined('USER_NAME') || define('USER_NAME', 'your username');
			defined('PASSWORD') || define('PASSWORD', 'your password');
	
			#edit database details
			'database' => [
		        'adapter'     => 'Mysql',
		        'host'        => 'localhost',
		        'username'    => 'your mysql root username',
		        'password'    => 'your password',
		        'dbname'      => 'your database',
		        'charset'     => 'utf8',
		    ]

4. Now open url 
		http://localhost/data

	and enter your username and password and you are now on table listing

5. On table listing page choose tables and and fill details by clicking expand(+) button before table name, and submit.


Your can give existing controller name in which you want your action that will append your action in last of the existing controller.
Model is created for table if not created before and View created in view folder in controler directory.


Thanks!


