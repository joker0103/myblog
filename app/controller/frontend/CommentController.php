<?php 
namespace app\controller\frontend;
use core\Controller;
use app\model\ArticleModel;
use app\model\CommentModel;
use app\model\CategoryModel;
use app\model\GoodModel;
use app\controller\frontend\ArticleController;
class CommentController extends Controller
{
	public function index()
	{
		//判断是否合法进入
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
		}else {
			$this->redirect('?p=frontend&c=Article&a=index', 3, "非法操作！");
		}
		//将当前页面的url地址存入cookie中，方便登录或注销时跳回当前页面
		setcookie('url', $_SERVER['REQUEST_URI']);
		$ArticleController = new ArticleController;
		//增加阅读数
		$ArticleController->addRead($id);
		//查询该用户是否对此文章点赞
		if (!isset($_SESSION['user'])) {
			$good = 2;
		}else{
			$user_id = $_SESSION['user']['id'];
			$where = "article_id={$id} AND user_id={$user_id}";
			$good = GoodModel::createInstance()->getCount($where);
		}
		$article = ArticleModel::createInstance()->findAllByLeft("article.id={$id}");
		//得到所有category父分类的id值
		$parentId= CategoryModel::createInstance()->getParentId();
		//获取无限极分类
		$categories = CategoryModel::createInstance()->limitlessLevelCategory(CategoryModel::createInstance()->findAll(), 0, 0, $parentId);
		//获得所有评论
		$where = "`comment`.article_id={$id}";
		$comments = CommentModel::createInstance()->findAllByLeft($where);
		//对评论进行无限极分类
		$comments = CommentModel::createInstance()->limitlessLevel($comments);
		//获得最新评论
		$lastComments = CommentModel::createInstance()->findLastComment($id);
		//使用smarty模板
		$this->s->assign(array(
			'article' => $article[0],
			'categories' => $categories,
			'lastComments' => $lastComments,
			'comments' => $comments,
			'good' => $good,
			));
		$this->s->display('frontend/article/content.html');
	}

 public function add()
    {
        $this->loginStatus();
        if (CommentModel::createInstance()->add(array(
            'user_id' => $_SESSION['user']['id'],
            'article_id' => $_GET['article_id'],
            'addtime' => time(),
            'content' => addslashes(str_replace('</script>', '', str_replace('<script>', '', $_POST['txaArticle']))),
            'parent_id' => $_POST['inpRevID'],
        ))) {
            // 添加成功
            $this->redirect("?p=frontend&c=Comment&a=index&id={$_GET['article_id']}", 3, '评论成功');
        } else {
            // 添加失败
            $this->redirect("?p=frontend&c=Comment&a=index&id={$_GET['article_id']}", 3, "评论失败，请稍后再试。");
        }
    }
}