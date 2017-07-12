<?php
/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

//edit your db config
$db = array(
    'adapter' => 'Mysql',
    "host" => 'localhost',
    "dbname" => "tmt",
    "port" => 3306,
    "username" => "root",
    "password" => 'root',
    "charset" => 'utf8',
);



$formDb = array(
    'adapter' => 'Mysql',
    "host" => 'localhost',
    "dbname" => "information_schema", //do not change this
    "port" => 3306,
    "username" => "root", //your root user username
    "password" => 'root', //password
    "charset" => 'utf8',
);

return new \Phalcon\Config([
    // 'database' => [
    //     'adapter'     => 'Mysql',
    //     'host'        => 'localhost',
    //     'username'    => 'root',
    //     'password'    => '',
    //     'dbname'      => 'test',
    //     'charset'     => 'utf8',
    // ],

    'database' => $db,
    'formDb' => $formDb,

    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',

        // This allows the baseUri to be understand project paths that are not in the root directory
        // of the webpspace.  This will break if the public/index.php entry point is moved or
        // possibly if the web server rewrite rules are changed. This can also be set to a static path.
        'baseUri'        => preg_replace('/public([\/\\\\])index.php$/', '', $_SERVER["PHP_SELF"]),
    ]
]);
