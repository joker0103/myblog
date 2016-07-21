<?php 
namespace app\controller\backend;
use core\Controller;
use app\model\CommentModel;
use app\model\ArticleModel;
use vendor\pager;
class CommentController extends Controller
{
	public function index()
	{
		//根据是否提交搜索条件对$where进行不同的赋值
		if (isset($_REQUEST['search'])) {
			$search = $_REQUEST['search'];
			$searchWhere = "`comment`.`content` LIKE '%$search%'";
		}else{
			$search = '';
			$searchWhere = '2>1';
		}
		$where = ($_SESSION['user']['power'] == 3) ? " {$searchWhere} AND b.user_id={$_SESSION['user']['id']} OR comment.user_id={$_SESSION['user']['id']} AND {$searchWhere}" :"{$searchWhere}";//根据不同权限分配查看的文章
		$pageSize = 5;  //每页显示两行
		$nowPage = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;//当前页
		$page = 5;  //每个页面显示几个页码
		$start = ($nowPage -1) * $pageSize;  //查询起始值
		$limit = "LIMIT {$start},{$pageSize}";   //构造limit语句
		$count = count(CommentModel::createInstance()->findAllByLeft($where));
		//连表查出所有的评论
		$comments = CommentModel::createInstance()->findAllByLeft($where, $limit);
		//将搜索关键字变成红色
		foreach ($comments as $comment) {
			$content = $comment->comment_content;
			$content = str_replace($search, "<font color='red'>{$search}</font>", $content);
			$comment->comment_content = $content;
		}
		$pager = new Pager($count, $page, $pageSize, $nowPage, 'index.php', array(
			'p' => 'backend',
		    'c' => 'Comment',
		    'a' => 'index',
		    'search' => $search,
		));
		$pagerHtml = $pager->showPage();
		//包含view文件
		$this->loadHtml('comment/index', array('comments' => $comments, 'search' => $search, 'pagerHtml' => $pagerHtml));
	}
	public function delete()
	{
		//判断是否为非法操作
		if (!isset($_GET['id']) && !isset($_POST['del'])) {
			$this->redirect('?p=backend&c=Comment&a=index', 3, '非法操作！！！');
		}	
		if (isset($_GET['id']) && !isset($_POST['del'])) {
			$id = $_GET['id'];
		}else if (!isset($_GET['id']) && isset($_POST['del'])) {
			$id = '';
			foreach ($_POST['del'] as  $value) {
			 	$id .="{$value},";
			 } 
			$id = rtrim($id,',');
		}

		//得到用户的权限值
		$power = $_SESSION['user']['power'];
		//根据不同权限值分配删除权限
		if ($power == 3) {
			//得到用户的id
			$user_id = $_SESSION['user']['id'];
			//查询出该用户的发表的文章的id
			$where = "user_id={$user_id}";
			$articles = ArticleModel::createInstance()->findAll($where);
			$article_id = '';
			foreach ($articles as $key => $value) {
				$article_id .= $value->id.",";
			}
			$article_id = rtrim($article_id, ',');
			$where = "user_id={$user_id} OR article_id IN ({$article_id}) AND id in ({$id})";
		}else {
			$where = "2>1";
		}
		if (CommentModel::createInstance()->deleteById($id, $where)) {
			$this->redirect('?p=backend&c=Comment&a=index', 3, '删除成功');
		}else {
			$this->redirect('?p=backend&c=Comment&a=index', 3, '删除失败');
		}

	}
}