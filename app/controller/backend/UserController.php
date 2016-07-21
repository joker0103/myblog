<?php 
namespace app\controller\backend;
use app\model\UserModel;
//继承Controller类
 class UserController extends \core\Controller
 {	
 	//显示用户列表的方法
 	public function index()
	{
		//判断登陆状态
		$this->loginStatus();
		//得到用户的权限，并根据权限分配功能
		$power = $_SESSION['user']['power'];
		$where = ($power == 1) ? '2>1' : 'id='.$_SESSION['user']['id'];
		//得到用户的信息
		$users = UserModel::createInstance()->findAll($where);
		//得到用户的权限转为对应的名字
		$users = UserMOdel::createInstance()->getPowerName($users);
		//方便传递多种信息
		$data = array(
				'users' => $users,
				'power' => $power,
			);
		$this->loadHtml('user/index', $data);
	}
	//增加用户的方法
	public function add()
	{
		//接受表单传来的信息
		if (!empty($_POST)) {
			//判断表单信息是否合法
			$this->testPostInfo();
			//验证两次密码是否相等
			if ($_POST['password'] != $_POST['repassword']) {
				$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER.'&a=add', 3, '注册失败，两次密码必须一致');
			}
			//接收表单数据
			$data = array (
				'name' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['username']))),
				'nickname' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['nickname']))),
				'email' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['email']))),
				'password' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['password']))),
				'registertime' => time(),
				);
			if (UserModel::createInstance()->add($data)) {
				$this->redirect("?p=".PLATFORM.'&c=Login&a=login', 3, '注册成功');
			}else {
				$this->redirect("?p=".PLATFORM.'&c=User&a=add', 3, '注册失败');
			}
		}
		$this->loadHtml('user/add');
	}
	//更新用户信息的方法
	public function update()
	{
		//验证登录状态
		$this->loginStatus();
		$id = $_GET['id'];
		//获取用户权限值
		$power = $_SESSION['user']['power'];
		if (!empty($_POST)) {
			//判断表单信息是否合法
			$this->testPostInfo($id);
			//接收表单传来的信息
			$data = array (
				'name' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['username']))),
				'nickname' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['nickname']))),
				'email' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['email']))),
				);
			//站长权限才可修改权限属性
			if ($power == 1) {
					$data['power'] = addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['power'])));
				}
			//两次密码一致，才可修改密码
			if ($_POST['password'] == $_POST['repassword']) {
				//站长权限且不是修改的自己的密码及修改自身的密码两种情况进行不同的逻辑判断
				if ($power == 1 && $id != $_SESSION['user']['id']) {
					$data['password'] =md5(md5($_POST['password']));
				}else if ($id == $_SESSION['user']['id']) {
					if ($_SESSION['user']['password'] == md5(md5($_POST['oldpassword']))) {
						$data['password'] =md5(md5($_POST['password']));
					}else{
						$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER."&a=update&id=$id", 3, '原密码错误，更新失败');
					}
				}else {
					$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER."&a=update&id=$id", 3, '非法操作');
				}
			}else {
				$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER."&a=update&id=$id", 3, '更新失败,两次密码必须一致');
			}
			//调用模型更新用户信息
			if (is_int(UserModel::createInstance()->updateById($id,$data))) {
				$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER.'&a=index', 3, '更新成功');
			}
		}
		$user = UserModel::createInstance()->findById($id);
		$data = array(
			'user' => $user,
			'power' => $power
			);
		$this->loadHtml('user/update', $data);
	}
	//删除用户的方法
	public function delete()
	{
		$power = $_SESSION['user']['power'];
		$this->loginStatus();
		if ($power == 1) {
			$id = $_GET['id'];
			if (UserModel::createInstance()->deleteById($id)) {
				$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER.'&a=index', 3, '删除成功');
			}
		}else {
			$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER.'&a=index', 3, '非法操作!!!');
		}
	}
	//判断表单提交信息是否合法
	public function testPostInfo($id = '')
	{
		$preg = "/^[a-z]{1}[a-z0-9A-Z]{3,11}$/i";
		if (!preg_match($preg, $_POST['username']) || !preg_match('/\d/', $_POST['username'])) {
			$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER.'&a='.ACTION."&id=$id", 3, '用户名必须为4-12位数字或大小写字母混合组成,且必须以字母开头');
		}
		/*if (!preg_match($preg, $_POST['password']) || !preg_match('/[0-9]{1,11}/', $_POST['password'])) {
			$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER.'&a='.ACTION."&id=$id", 300, '密码名必须由6-12位数字或大小写字母组成,且必须以字母开头');
		}*/
		if (!strpbrk($_POST['email'], '@')) {
			$this->redirect("?p=".PLATFORM.'&c='.CONTROLLER.'&a='.ACTION."&id=$id", 3, '邮箱格式不正确');
		}
		
	}
 }