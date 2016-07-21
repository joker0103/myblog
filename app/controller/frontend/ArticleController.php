<?php 
namespace app\controller\frontend;
use core\Controller;
use app\model\ArticleModel;
use app\model\CategoryModel;
use vendor\Pager;
use app\model\GoodModel;
class ArticleController extends Controller
{
	public function index()
	{
		$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
		$categoryId = isset($_REQUEST['categoryId']) ? $_REQUEST['categoryId'] : 0;
        $where = '2 > 1';
        if ($search) {
            // 用户传递了搜索条件
            $where .= " AND `article`.`title` LIKE '%{$search}%'";
        }
        if ($categoryId) {
        	// 用户传递了categoryId值
            $where .= " AND `article`.`category_id`='{$categoryId}'";
        }
        setcookie('url',$_SERVER['REQUEST_URI']);
		$pageSize = 3;  //每页显示两行
		$nowPage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;//当前页
		$page = 5;  //每个页面显示几个页码
		$start = ($nowPage - 1) * $pageSize;
		$limit = "LIMIT {$start},{$pageSize}";
		//获取文章列表数据
		$articles = ArticleModel::createInstance()->findAllByLeft($where, $limit);
		//对搜索关键字加颜色处理
		foreach ($articles as $article) {
			$title = $article->title;
			$title = str_replace($search, "<font color='red'>{$search}</font>", $title);
			$article->title = $title;
		}
		//获取所有文章的第一张图片的url
		$articles = ArticleModel::createInstance()->getFirstImgUrl($articles);
		//得到所有category父分类的id值
		$parentId= CategoryModel::createInstance()->getParentId();
		//获取无限极分类
		$categories = CategoryModel::createInstance()->limitlessLevelCategory(CategoryModel::createInstance()->findAll(), 0, 0, $parentId);
		$count = count(ArticleModel::createInstance()->findAllByLeft($where));//总页数
		//分页类
		$pager = new Pager($count, $page, $pageSize, $nowPage, 'index.php', array(
				'p' => 'frontend',
    			'c' => 'Article',
    			'a' => 'index',
    			'search' => $search,
    			'categoryId' => $categoryId,
			));
		$pagerHtml = $pager->showPage();
		$this->s->assign(array(
				'articles' => $articles,
				'categories' => $categories,
				'pagerHtml' => $pagerHtml,
				'categoryId' => $categoryId,
				'search' => $search,
			));
		$this->s->display('frontend/article/index.html');
	}
	//增加点赞数的方法
	public function addGood()
	{
		if (isset($_GET['id'])) {
			$article_id = $_GET['id'];
		}else {
			$this->redirect("?p=frontend&c=Articel&a=index", 3, '非法操作');
		}
		$this->loginStatus();
		$user_id = $_SESSION['user']['id'];
		$where = "article_id={$article_id} AND user_id={$user_id}";
		//查询此用户是否为此文章点赞，判断是否重复点赞
		if (GoodModel::createInstance()->getCount($where) != 1) {
			if (ArticleModel::createInstance()->addGood($article_id)) {
				//在点赞表中添加信息
				GoodModel::createInstance()->add(array(
						'user_id' => $user_id,
						'article_id' => $article_id,
					));
				$this->redirect("?p=frontend&c=Comment&a=index&id={$article_id}", 3, '点赞成功');
			}else {
				$this->redirect("?p=frontend&c=Comment&a=index&id={$article_id}", 3, '服务器罢工了');
			}
		}else {
			$this->redirect("?p=frontend&c=Comment&a=index&id={$article_id}", 3, '不能重复点赞');
		}
		if (!ArticleModel::createInstance()->addGood($article_id)) {
			$this->redirect("?p=frontend&c=Comment&a=index&id={$article_id}", 3, '服务器罢工了');
		}
	}
	//增加阅读数的方法
	public function addRead($article_id)
	{
		if (!ArticleModel::createInstance()->addRead($article_id)) {
			$this->redirect("?p=frontend&c=Comment&a=index&id={$article_id}", 3, '服务器罢工了');
		}
	}
}