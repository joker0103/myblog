<?php 
namespace app\controller\backend;
use core\Controller;
use app\model\ArticleModel;
use app\model\CategoryModel;
use app\model\UserModel;
use vendor\Pager;
class ArticleController extends Controller
{
	public function index()
	{
		//得到用户的权限，并根据权限分配功能
		$power = $_SESSION['user']['power'];
		$where = ($power != 1) ? '`article`.user_id='.$_SESSION['user']['id'] : '2>1';
		//判断是否提交了搜索的相关参数，并构成where语句
		$page = 3;//一共显示多少页
		$pageSize = 2;//每页显示多少条数据
		$nowPage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
		$start = ($nowPage-1) * $pageSize;
		$category = isset($_REQUEST['category']) ? $_REQUEST['category'] : 0;
		if ($category) {
			$where .= " AND `category_id` = '{$category}'";
		}
		$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : 0;
		if ($status) {
			$where .= " AND `status` = '{$status}'";
		}
		$top = isset($_REQUEST['istop']) ? $_REQUEST['istop'] : 0;
		if ($top) {
			$where .= " AND `top`='{$top}'";
		}
		$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
		if ($search) {
			$where .= " AND `title` like '%{$search}%'";
		}
		$limit = " limit {$start},{$pageSize}";
		//获得所有的文章数据
		$articles = ArticleModel::createInstance()->findAllByLeft($where, $limit);
		//对搜索关键字加颜色处理
		foreach ($articles as $article) {
			$title = $article->title;
			$title = str_replace($search, "<font color='red'>{$search}</font>", $title);
			$article->title = $title;
		}
		//将article对象数组中的status属性转为相对应的字符
		$articles = ArticleModel::createInstance()->getStatusName($articles);
		//得到所有category父分类的id值
		$parentId= CategoryModel::createInstance()->getParentId();
		//无限极分类
		$categories = CategoryModel::createInstance()->findAll();
		$categories = CategoryModel::createInstance()->limitlessLevelCategory($categories, 0 , 0, $parentId);
		$count = ArticleModel::createInstance()->getCount($where);
		$pager = new Pager($count, $page, $pageSize, $nowPage, "index.php", array(
			'p' => 'backend',
		    'a' => 'index',
		    'c' => 'Article',
		    'status' => $status,
		    'category' => $category,
		    'istop' => $top,
		    'search' => $search,
		));
		$pagerHtml = $pager->showPage();
		$this->loadHtml('article/index', array(
												'articles' => $articles,
												'categories' => $categories,
												'category' => $category,
												'top' => $top,
												'status' => $status,
												'search' => $search,
												'pagerHtml' => $pagerHtml,
												));
	}
	public function add()
	{
		if (!empty($_POST)) {
			$username = $_SESSION['user']['username'];
			$id = $_SESSION['user']['id'];
			$top = (isset($_POST['isTop'])) ? 1:2 ;
			$addtime = strtotime($_POST['PostTime']);
			$data = array (
				'status' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['Status']))),
				'title' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['Title']))),
				'content' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['Content']))),
				'category_id' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['CateID']))),
				'addtime' => $addtime,
				'top' =>$top,
				'user_id' => $id,
				);
			if (ArticleModel::createInstance()->add($data)) {
				$this->redirect('?p=backend&c=Article&a=index', 3, '添加成功');
			}else{
				$this->redirect('?p=backend&c=Article&a=add', 3, '添加失败');
			}
		}else{
			//得到所有category父分类的id值
			$parentId= CategoryModel::createInstance()->getParentId();
			//无限极分类
			$categories = CategoryModel::createInstance()->findAll();
			$categories = CategoryModel::createInstance()->limitlessLevelCategory($categories, 0, 0, $parentId);
			$this->loadHtml('article/add',array('categories' => $categories));
		}
	}
	public function delete()
	{
		//判断是否有id值传过来，如果没传，则为非法操作
		if (!isset($_REQUEST['id'])) {
			$this->redirect('?p=backend&c=Article&a=index', 3, "非法操作");
		}
		$id = $_REQUEST['id'];
		$power = $_SESSION['user']['power'];
		$where = ($power == 3) ? '`user_id`='.$_SESSION['user']['id'] : '2>1';
		if (is_numeric(ArticleModel::createInstance()->deleteById($id, $where))) {
			$this->redirect('?p=backend&c=Article&a=index', 3, "删除成功");
		}else{
			$this->redirect('?p=backend&c=Article&a=index', 3, "删除失败");
		}
	}
	public function update()
	{
		//判断是否有id值传过来，如果没传，则为非法操作
		if (!isset($_REQUEST['id'])) {
			$this->redirect('?p=backend&c=Article&a=index', 3, "非法操作");
		}
		$id = $_REQUEST['id'];
		if (!empty($_POST)) {
			$top = (isset($_POST['isTop'])) ? 1:2 ;//是否置顶
			$addtime = strtotime($_POST['PostTime']);//添加时间
			$power = $_SESSION['user']['power'];//权限值
			$where = ($power == 3) ? '`user_id`='.$_SESSION['user']['id'] : '2>1';
			$data = array (
				'status' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['Status']))),
				'title' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['Title']))),
				'content' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['Content']))),
				'category_id' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['CateID']))),
				'addtime' => $addtime,
				'top' =>$top,
				);
			if (is_numeric(ArticleModel::createInstance()->updateById($id, $data, $where))) {
				$this->redirect('?p=backend&c=Article&a=index', 3, "修改成功");
			}else {
				$this->redirect('?p=backend&c=Article&a=update', 3, "修改失败");
			}
		}else {
			//得到所有category父分类的id值
			$parentId= CategoryModel::createInstance()->getParentId();
			$power = $_SESSION['user']['power'];
			$where = ($power == 3) ? '`user_id`='.$_SESSION['user']['id'] : '2>1';
			$article = ArticleModel::createInstance()->findOne("id={$id} AND {$where}");
			$categories = CategoryModel::createInstance()->limitlessLevelCategory(CategoryModel::createInstance()->findAll(), 0, 0, $parentId);
			$this->loadHtml('article/update', array('article' => $article, 'categories' => $categories));
		}
	}
}