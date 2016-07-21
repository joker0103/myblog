<?php 
namespace app\controller\backend;
use core\Controller;
use app\model\UserModel;
class FrameController extends Controller
{
	//不同的action的值调用不同的方法，加载不同的模块
	public function __construct()
	{
		parent::__construct();
		$this->loginStatus();
	}
	
	public function frame()
	{	
		$this->loadHtml('frame/frame');
	}
	public function content()
	{
		$this->loadHtml('frame/content');
	}
	public function menu()
	{
		$this->loadHtml('frame/menu');
	}
	public function top()
	{
		$user = $_SESSION['user'];
		switch ($user['power']) {
			case 1:
				$user['power'] = '大站长';
				break;
			case 2:
				$user['power'] = '管理员';
				break;
			default:
				$user['power'] = '小博主';
				break;
		}
		$data = array(
				'user' => $user,
			);
		$this->loadHtml('frame/top', $data);
	}
} 