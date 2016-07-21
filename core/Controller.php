<?php 
namespace core;
use vendor\Smarty;
class Controller
{	
	protected $s;
	public function __construct()
	{
		$this->createSmartyObj();
	}
	//跳转的方法
	protected  function redirect($url, $time, $message, $type = 1)
	{	
		
		if ($type == 2) {
            echo "{$message}，正在为您跳转......";
			header("refresh:{$time};url={$url}");
			exit();
        } else {
            // 显示用户友好的提示信息
            require VIEW_PATH . '/tip.html';
            die();
        }
	}
	//包含视图文件的方法
	protected function loadHtml($htmlName, $data = array())
	{	
		foreach ($data as $key => $value) {
			$$key = $value;//可变变量的知识
		}
		require(VIEW_PATH.'/'.PLATFORM."/{$htmlName}.html");
	}
	//判断是否登陆的方法
	protected function loginStatus()
	{
		if (!isset($_SESSION['user'])) {
			$this->redirect('?p=backend&c=Login&a=login', 3, '请登陆后在访问');
		}
	}
	public function createSmartyObj()
	{
		$s = new Smarty();
        // templates_c放到系统的临时目录
        $s->setCompileDir(sys_get_temp_dir() . '/templates_c');
        // configs 放到 app/config
        $s->setConfigDir('./app/config');
        // templates 放到 app/view
        $s->setTemplateDir(VIEW_PATH);
        $s->left_delimiter = '<{';
        $s->right_delimiter = '}>';
        $this->s = $s;
	}
}