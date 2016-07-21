<?php 
namespace app\controller\backend;
use core\Controller;
use app\model\CategoryModel;
class CategoryController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->loginStatus();
	}
	public function index()
	{
		$parentId= CategoryModel::createInstance()->getParentId();
		$categoryId = array(0);
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
		}else {
			$id = 0;
		}
		$categoryId = CategoryModel::createInstance()->getCategoryId($id);
		$categories = CategoryModel::createInstance()->limitlessLevelCategory(CategoryModel::createInstance()->findAll(), 0, 0,$categoryId);
		$this->loadHtml('category/index', array('categories' => $categories, 'parentId' => $parentId));
	}
	public function update()
	{
		if (!isset($_GET['id'])) {
			$this->redirect('?p=backend&c=Frame&a=frame', 3, '非法操作!');
		}
		$id = $_GET['id'];
		if (!empty($_POST)) {
			$data = array (
				'name' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['name']))),
				'nickname' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['nickname']))),
				'sort' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['sort']))),
				'parent_id' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['parentId']))),
				);
			if (is_numeric(CategoryModel::createInstance()->updateById($id, $data))) {
				$this->redirect('?p=backend&c=Category&a=index', 3, '修改成功');
			}else{
				$this->redirect('?p=backend&c=Category&a=update', 3, '修改失败');
			}
		}else{
			//得到所有父分类的id
			$parentId= CategoryModel::createInstance()->getParentId();
			//得到无限极分类的category对象数组
			$categories = CategoryModel::createInstance()->limitlessLevelCategory(CategoryModel::createInstance()->findAll(), 0, 0, $parentId);
			$category = CategoryModel::createInstance()->findById($id);
			$this->loadHtml('category/update', array('category' => $category, 'categories' => $categories,));
		}
	}
	public function add()
	{
		if (!empty($_POST)) {
			$data = array (
				'name' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['name']))),
				'nickname' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['nickname']))),
				'sort' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['sort']))),
				'parent_id' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['parentId']))),
				);
			if (CategoryModel::createInstance()->add($data)) {
				$this->redirect('?p=backend&c=Category&a=index', 3, '添加成功');
			}else{
				$this->redirect('?p=backend&c=Category&a=add', 3, '添加失败');
			}
		}else{
			$parentId= CategoryModel::createInstance()->getParentId();
			$categories = CategoryModel::createInstance()->limitlessLevelCategory(CategoryModel::createInstance()->findAll(), 0, 0, $parentId);
			$this->loadHtml('category/add', array('categories' => $categories));
		}
	}
	public function delete()
	{
		if (!isset($_GET['id'])) {
			$this->redirect('?p=backend&c=Frame&a=frame', 3, '非法操作!');
		}
		$id = $_GET['id'];
		//如果有子分类，则不删除
		if (CategoryModel::createInstance()->getCount("parent_id={$id}") >0) {
			$this->redirect('?p=backend&c=Category&a=index', 3, "删除失败，不能删除有子分类的分类");
		}
		if (CategoryModel::createInstance()->deleteById($id)) {
			$this->redirect('?p=backend&c=Category&a=index', 3, "删除成功");
		}else{
			$this->redirect('?p=backend&c=Category&a=index', 3, "删除失败，请稍后再试");
		}
	}
}
