<?php 
namespace app\config;
class Config 
{
	public static function config()
	{
		$config = array(
 			'type' => "mysql",
 			'prot' => '3306',
 			'user' => "root",
 			'pass' => "932057",
 			'dbname' => "blog",
 			'host' => 'localhost',
 			'charset' => 'utf8',
 		);
 		return $config;
	}
}