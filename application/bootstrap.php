<?php

//set to US central time
date_default_timezone_set('Asia/Shanghai');

// Step 1: Check to see if the applicaiton environment is already setup
if (isset($bootstrap) && $bootstrap) {

	// Step 1a: Add our library directory to the include path so that
	// PHP can find the Zend Framework classes.

	set_include_path('./library'
					. PATH_SEPARATOR
					. './application/common'
					. PATH_SEPARATOR . get_include_path());

	// Step 1b: Set up autoload.
	// This is a nifty trick that allows ZF to load classes automatically so that
	// you don't have to litter your code with 'include' or 'require' statements.
	require_once "Zend/Loader.php";
	require_once 'Zend/Loader/Autoloader.php';
	$loader = Zend_Loader_Autoloader::getInstance();
//	$loader->registerNamespace('Main_');
	$loader->setFallbackAutoloader(true);
	$loader->suppressNotFoundWarnings(false);
	Zend_Session::start();
}

// Step 2: Get the front controller.
// The Zend_Front_Controller class implements the Singleton pattern, which is a
// design pattern used to ensure there is only one instance of
// Zend_Front_Controller created on each request.
$frontController = Zend_Controller_Front::getInstance();

$frontController->throwExceptions(false);

// Step 3: Point the front controller to your action controller directory.
$frontController->setControllerDirectory(array(
        'default' => './application/Default/controllers',
        'admin'   => './application/Admin/controllers',
    )
);

// Step 4: Set the current environment
// Set a variable in the front controller indicating the current environment --
// commonly one of development, staging, testing, production, but wholly
// dependent on your organization and site's needs.
$frontController->setParam('env', 'development');

// load configuration
$config = new Zend_Config_Ini('./application/Config/config.ini', 'general');

$registry = Zend_Registry::getInstance();
$registry->set('config', $config);


//setup database
$dbAdapter = Zend_Db::factory($config->db);
$registry->set('dbAdapter', $dbAdapter);
$registry->set('dbTbPrefix',$config->db->params->prefix);
Zend_Db_Table::setDefaultAdapter($dbAdapter);

$registry->set('adminPageSize', 30);
$registry->set('adminPageRange', 10);

$registry->set('siteEmail','info@ecmat.org');
$registry->set('siteName','CAMT');

//set root path
$rootPath = dirname(dirname(__FILE__));
$registry->set('rootPath',$rootPath);
$registry->set('uploadPath',"/resources/files/");
$registry->set('newsImagePath',"/resources/newsImage/");
$registry->set('newsVideoPath',"/resources/newsVideo/");
$registry->set('albumnPath',"/resources/photos/");

$fileTypes = array('pdf','doc','docx');
$registry->set('fileTypes',$fileTypes);
