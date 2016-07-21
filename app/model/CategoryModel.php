<?php 
namespace app\model;
use core\Model;
class CategoryModel extends Model
{
	public static $table = 'category';
	//无限极分类的方法
	public function limitlessLevelCategory($categories, $level = 0, $parentId = 0, $categoryId = array(0))
	{
		//静态数组，用来保存加了level属性后的category对象
		static $limitlessLevelCategories = array();
		foreach ($categories as $category) {
			//如果父id与传递过来的id相等，则是该父分类的子分类，同时判断是否存在于分类id数组中
			if ($category->parent_id == $parentId && in_array($parentId, $categoryId)) {
				$category->level = $level;
				$limitlessLevelCategories[] = $category;
				$this->limitlessLevelCategory($categories, $level+1, $category->id, $categoryId);
			}
		}
		return $limitlessLevelCategories;
	}
	//得到由被选择的分类id组成的数组的方法
	public function getCategoryId($id)
	{
		//判断对应SESSION值是否存在，对$categoryId进行初始化赋值
		if (isset($_SESSION['category'])) {
			$categoryId = $_SESSION['category'];
		}else{
			$categoryId = array();
		}
			//如果传递过来的id在数组中，则删除该id，如果不存在，则加入数组中
			if (in_array($id, $categoryId)) {
				foreach ($categoryId as $key => $value) {
					if ($value == $id) {
						unset($categoryId[$key]);
					}
				}
			}else {
				$categoryId[] = $id;
			}
			//将0加入到分类id数组中
			if (!in_array(0,$categoryId)) {
				$categoryId[] = 0;
			}
		$_SESSION['category'] = $categoryId;
		return $categoryId;
	}
	//得到所有父类的id的方法
	public function getParentId()
	{
		$sql = "SELECT DISTINCT parent_id FROM category";
		$parent_id = $this->getAll($sql);
		foreach ($parent_id as $key => $value) {
			$parentId[] = $value->parent_id; 
		}
		return $parentId;
	}
} 