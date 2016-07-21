<?php 
namespace core;
class Application
{
	//依次调用方法
	public static function run()
	{
		//设置字符集
		self::setCharset();
		//定义常量
		self::defineConst();
		//定义自动加载器
		self::defineAutoloader();
		//开启session
		self::openSession();
		//分发路由
		self::disPatchRoute();
	}
	protected static function setCharset()
	{
		header("content-type:text/html;charset=utf-8");
	}
	protected static function defineConst()
	{
		//定义路由常量
		$p = isset($_GET['p']) ? $_GET['p'] : 'frontend';
		define('PLATFORM', $p);
		$c = isset($_GET['c']) ? $_GET['c'] : 'Article';
		define('CONTROLLER', $c);
		$a = isset($_GET['a']) ? $_GET['a'] : 'index';
		define('ACTION', $a);
		//定义路径常量
		define("VIEW_PATH", './app/view');
		define("CONFIG_PATH", './app/config');
	}
	protected static function defineAutoloader()
	{
		spl_autoload_register("self::loadClass");
	}
	//实现类文件的自动加载，利用名字空间
	protected static function loadClass($classname)
	{	 
		$classname = str_replace('\\', "/", $classname);
		if (is_file($classname.'.php')) {
			require($classname.".php");	
		}
		//echo $classname."</br>";
	}
	protected static function disPatchRoute()
	{
		$c = CONTROLLER.'Controller';
		$c = "app\\controller\\".PLATFORM."\\".$c;
		//调用控制器
		$controller = new $c();
		//调用控制器的方法
		$controller->{ACTION}();
	}
	protected static function openSession()
	{
		session_start();
	}
}
