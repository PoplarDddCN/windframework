<?php
/**
 * @author xiaoxia xu <x_824@sina.com> 2010-12-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license
 */
session_start();
$_SESSION['start'] = microtime();
header("Content-type: text/html; charset=utf8");
define('R_P', dirname(__FILE__) . '/');
define('IS_DEBUG', true);
/* 框架文件路径 */
define('FREAMWORK_PATH', R_P . '/../../wind/');
/* 缓存文件路径 */
define('COMPILE_PATH', R_P . 'compile/');
require_once (FREAMWORK_PATH . '/wind.php');
echo R_P . 'config.xml';
W::application('formdemo', R_P . 'config.xml')->run();
