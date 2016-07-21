<?php 
namespace core;
use app\config\Config;
class Model extends \vendor\PDOWrapper
{
	public function __construct()
 	{
 		//调用config类的方法，得到数据库的配置信息
 		$config = Config::config();
 		//调用父类的构造方法，通过Pdo连接数据库，得到pdo对象
 		parent::__construct($config);
 	}
 	//创建对象的函数
 	public static function createInstance($classname = null)
 	{
 		//静态延时绑定调用子类的静态属性
 		$table = static::$table;
 		//用子类的名字作为对象的下标，静态变量，只会被初始化一次，作用域为本方法内
 		static $instance = array();
 		if ($classname === null) {
 			$classname = get_called_class();
 		}
 		//用数组来保存对象的名字，单例工厂模式，同名的对象只实例化一次
 		if (!isset($instance[$classname])) {
 			//数组instacne只在此方法内有实际的意义
 			$instance[$classname] = new $classname();
 		}
 		return $instance[$classname];
 	}
 	//得到所有数据的方法
 	public function findAll($where = '2>1')
 	{
 		$table = static::$table;
 		$sql = "SELECT * FROM {$table} WHERE {$where}";
 		return $this->getAll($sql);
 	}
 	//通过id得到一条数据的方法
 	public function findById($id)
 	{	
 		$table = static::$table;
 		$sql = "SELECT * from {$table} WHERE id = $id";
 		return $this->getOne($sql);
 	}
 	//删除数据
 	public function deleteById($id, $where='2>1')
 	{	
 		$table = static::$table;
 		$sql = "DELETE FROM {$table} WHERE id in ($id) AND {$where}";
 		return $this->exec($sql);
 	}
 	//添加数据
 	public function add($data)
 	{	
 		$table = static::$table;
 		$columns = '';
 		$values = '';
 		foreach ($data as $key => $value) {
 			$columns .="{$key},"; 
 			$values .="'{$value}',"; 
 		}
 		$columns = rtrim($columns, ',');//将字符串末尾的逗号去掉
 		$values = rtrim($values, ',');
 		$sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
 		return $this->exec($sql);
 	}
 	//更新数据
 	public function updateById($id, $data, $where='2>1')
 	{	
 		$table = static::$table;
 		$sets = '';
 		foreach ($data as $key => $value) {
 			$sets .="{$key}='{$value}',"; 
 		}
 		$sets = rtrim($sets, ',');//将字符串末尾的逗号去掉
 		$sql = "UPDATE {$table} SET {$sets} WHERE id=$id AND {$where}";
 		return $this->exec($sql);
 	}
 	//根据用户名更新数据
 	public function updateByName($name, $data)
 	{
 		$table = static::$table;
 		$sets = '';
 		foreach ($data as $key => $value) {
 			$sets .="{$key}='{$value}',"; 
 		}
 		$sets = rtrim($sets, ',');//将字符串末尾的逗号去掉
 		$sql = "UPDATE {$table} SET {$sets} WHERE name='$name'";
 		return $this->exec($sql);
 	}
 	//得到总记录数
 	public function getCount($where = '2>1')
 	{
 		$table = static::$table;
 		$sql = "SELECT count(*) as count FROM {$table} WHERE {$where}";
 		return $this->getOne($sql)->count;
 	}
 	//通过条件得到一条记录的方法
 	public function findOne($where)
 	{
 		$table = static::$table;
 		$sql = "SELECT * FROM {$table} WHERE {$where}";
 		return $this->getOne($sql);
 	}
}