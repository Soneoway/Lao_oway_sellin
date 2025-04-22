<?php
//echo "test";
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// header("Location: http://warehouse4.opposhop.vn/");
// $_time_start = microtime(true);
// require_once(__DIR__ . '/../library/PhpConsole/__autoload.php');
// PhpConsole\Helper::register();

// $handler = PhpConsole\Handler::getInstance();
// $handler->start();
// Define path to application directory
ini_set('memory_limit', -1);


defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

if (file_exists(__DIR__ . '/../library/PhpConsole/__autoload.php') && APPLICATION_ENV == 'development') {
    require_once(__DIR__ . '/../library/PhpConsole/__autoload.php');
    PhpConsole\Helper::register();

    $handler = PhpConsole\Handler::getInstance();
    $handler->start();
} else {
    require_once(__DIR__ . '/../library/My/Fake_PC.php');
}

/** Zend_Application */
require_once 'Zend/Application.php';
/** Custom configuration */
include_once APPLICATION_PATH . '/configs/common.conf.php';
date_default_timezone_set('Asia/Saigon');

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

//start : check force reset password
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
if(isset($userStorage) && $userStorage && $userStorage->re_pwd == '1' && !in_array($_SERVER['REQUEST_URI'],['/user/change-pass','/user/check-pass'])){
    header('Location: /user/change-pass');
}
//end : check force reset password

$application->bootstrap()
            ->run();

// $_end_start = microtime(true);

// PC::debug(($_end_start - $_time_start)." seconds", "Exec Time");
