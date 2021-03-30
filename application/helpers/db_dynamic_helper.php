<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function change_db($hostname, $username, $password, $database)
{
	$config_app = array(
		//'hostname'=>'localhost', 'username'=>'root', 'password'=>'12345678', 'database'=>$name_db, 'dbdriver'=>'mysqli',
		//'hostname'=>'192.168.100.144,49257\SQLEXPRESS', 'username'=>'sa', 'password'=>'12345678', 'database'=>$name_db, 'dbdriver'=>'sqlsrv',
		'hostname'=>$hostname, 'username'=>$username, 'password'=>$password, 'database'=>$database, 'dbdriver'=>'sqlsrv',
		'dbprefix'=>'',
		'pconnect'=>FALSE,
		'db_debug'=>FALSE
	);
    return $config_app;
	
}