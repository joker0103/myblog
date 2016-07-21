<?php 
namespace app\controller\backend;
use core\Controller;
use app\model\UserModel;
use vendor\Captcha;
class LoginController extends Controller
{
	public function login()
	{
		//判断表单是否提交，如果未提交，包含登陆文件
		if (empty($_POST)) {
			$this->loadHtml('login/login', array());
		}else{
			//接收表单传来的用户信息
			$username =  addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['username'])));
			$password=  addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['password'])));
			$where = "name='$username' AND password='$password'";
			//判断验证码是否正确，不正确调回
			if ($_SESSION['captcha'] != strtolower($_POST['edtCaptcha'])) {
				$this->redirect('?p=backend&c=Login&a=login', 3, '验证码错误');
			}
			//判断用户信息是否合法，如果不合法，则跳转至登陆页面,如果合法，则跳转至后台首页
			$user = UserModel::createInstance()->findOne($where);
			$user = UserModel::createInstance()->getPowerName($user);
			if ($user) {
				$user = array(
						'username' => $user->name,
						'id' => $user->id,
						'nickname' => $user->nickname,
						'lastloginip' => $user->lastloginip,
						'lastlogintime' => $user->lastlogintime,
						'email' => $user->email,
						'password' => $user->password,
						'power' => $user->power,
						'powerName' => $user->powerName,
					); 
				$_SESSION['user'] = $user;
				//登陆成功后，将最新的登录时间和ip地址更新到用户列表中
				$data = array(
						'lastlogintime' => time(),
						'lastloginip' => $_SERVER['REMOTE_ADDR'],
					);
				UserModel::createInstance()->updateByName($username, $data);
				//判断是否设置$_COOKIE['url']，并制定不同的url跳转地址
				$url = isset($_COOKIE['url']) ? $_COOKIE['url'] : "?p=frontend&c=Article&a=index";
				$this->redirect("$url", 3, '登陆成功');
			}else{
				$this->redirect('?p=backend&c=Login&a=login', 3, '登录失败');
			}
		}
	}
	//验证码的方法
	public function captcha()
	{
		$c = new Captcha;//验证码类
		$c->generateCode();
		$_SESSION['captcha'] = $c->getCode();
	}
	//退出登录的方法
	public function logout()
	{
		if (!isset($_SESSION['user'])) {
			$this->redirect('?p=backend&c=Login&a=login', 3, '非法操作');
		}
		//判断是否设置$_COOKIE['url']，并制定不同的url跳转地址
		$url = isset($_COOKIE['url']) ? $_COOKIE['url'] : "?p=frontend&c=Article&a=index";
		session_destroy();
		$this->redirect("$url", 3, '注销成功');
	}
}  