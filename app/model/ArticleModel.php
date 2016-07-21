<?php 
namespace app\model;
use core\Model;
class ArticleModel extends Model
{
	public static $table = 'article';
	//连表查询数据，返回对象数组
	public function findAllByLeft($where='2>1', $limit='')
	{
		$sql = "SELECT article.*,b.name AS user_name,c.name AS category_name 
				,count(d.id) AS count FROM article
				LEFT JOIN user AS b ON `article`.user_id=b.id
				LEFT JOIN category AS c ON `article`.category_id=c.id 
				LEFT JOIN comment  AS d ON d.article_id=`article`.id 
				WHERE {$where} GROUP BY `article`.id {$limit}";
		$articles = $this->getAll($sql);
		return $articles;
	}
	//将status的数值转为字符
	public function getStatusName($articles)
	{
		foreach ($articles as $value) {
			switch ($value->status) {
				case 1:
					$value->status = '草稿';
					break;
				case 2:
					$value->status = '公开';
					break;
				default:
					$value->status = '隐藏';
					break;
			}
		}
		return $articles;
	}
	// 提取所有文章的第一张图片
	public function getFirstImgUrl($articles)
	{
        $pregex = "/<img.+>?/";
        foreach($articles as $article) {
            $matchs = array();
            preg_match($pregex, $article->content, $matchs);
            $firstImgUrl = "";
            if (isset($matchs[0])) {
                $firstImgUrl = $matchs[0];
            }
            $article->firstImgUrl = $firstImgUrl; 
        }
        return $articles;
	}
	public function addRead($id)
	{
		$sql = "UPDATE article SET read_count=read_count+1 WHERE id={$id}";
		return $this->exec($sql);
	}
	public function addGood($id)
	{
		$sql = "UPDATE article SET good_count=good_count+1 WHERE id={$id}";
		return $this->exec($sql);
	}
}