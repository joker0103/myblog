<?php 
namespace app\model;
use core\Model;
class CommentModel extends Model
{
	public static $table = 'comment';
	public function findAllByLeft($where = '2>1', $limit='')
	{
		$sql = "SELECT comment.id,d.content,comment.parent_id,
			comment.content AS comment_content,
			b.title AS article_title,comment.addtime,
			c.name AS user_name
			FROM `comment`
			LEFT JOIN `article` AS b ON comment.article_id = b.id
			LEFT JOIN `user` AS c ON comment.user_id=c.id 
			LEFT JOIN `comment` AS d ON comment.parent_id=d.id	
			WHERE {$where} {$limit}";
		$comments = $this->getAll($sql);
		return $comments;
	}
	//无限极分类的方法
	public function limitlessLevel($comments, $level = 0, $parentId = 0)
	{
		//静态变量，保存加入level属性后的对象数组
		static $limitless = array();
		foreach ($comments as $key => $value) {
			if ($value->parent_id == $parentId) {
				$value->level = $level;
				$limitless[] = $value;
				$this->limitlessLevel($comments, $level+1, $value->id);
			}
		}
		return $limitless;
	}
	public function findLastComment($id)
	{
		$sql = "SELECT * FROM comment WHERE article_id={$id} order BY addtime desc limit 0,4 ";
		//echo $sql;exit();
		return $this->getAll($sql);
	}
	public function limitlessLevelComment($comments, $parentId = 0)
    {
        $treeComments = array();
        foreach ($comments as $comment) {
            if ($comment->parent_id == $parentId) {
                // 寻找评论的子评论
                $comment->childrens = $this->limitlessLevelComment($comments, $comment->id);
                $treeComments[] = $comment;
            }
        }
        return $treeComments;
    }
} 