<?php
require_once "Medoo.php";
use Medoo\Medoo;

$db = new Medoo([
	'type' => 'mysql',
	'host' => localhost,
	'database' => db_name,
	'username' => db_username,
	'password' => db_password,
	'charset' => 'utf8mb4',
	'collation' => 'utf8mb4_general_ci',
	'prefix' => prefix,
	'option' => [
		PDO::ATTR_CASE => PDO::CASE_NATURAL,
		PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
	]
]);