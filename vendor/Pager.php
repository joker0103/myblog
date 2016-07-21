<?php

namespace vendor;

class Pager
{
	private $total;     // 总共有多少条记录
	private $pagenum;   // 分成多少页
	private $page;      //显示多少页
	private $startPage; //起始页
	private $endPage;   //结束页
	private $pagesize;  // 每页多少条记录
	private $current;   // 当前所在的页数
	private $url;       // url
	private $allPage;   //所有页
	private $first;	    // 首页
	private $last;	    // 末页
	private $prev;	    // 上一页
	private $next;	    // 下一页

	/**
	 * 构造函数
	 * @access public
	 * @param $total number 总的记录数
	 * @param $pagesize number 每页的记录数
	 * @param $current number 当前所在页
	 * @param $script string 当前请求的脚本名称,默认为空
	 * @param $params array url所携带的参数,默认为空
	 */
	public function __construct($total, $page, $pagesize, $current, $script = '', $params = array())
	{
		$this->total = $total;
		$this->page = $page;
		$this->pagesize = $pagesize;
		$this->pagenum = $this->getNum();
		$this->current = $current;
		
		//设置url
		$p = array();
		foreach ($params as $k => $v) {
			$p[] = "$k=$v";
		}
		$this->url = $script . '?' . implode('&', $p) . '&page=';

		$this->first = $this->getFirst();
		$this->prev = $this->getPrev();
		$this->allPage = $this->getAllPage();
		$this->next = $this->getNext();
		$this->last = $this->getLast();
	}

	private function getNum()
	{
		return ceil($this->total / $this->pagesize);
	}

	private function getFirst()
	{
		if ($this->current == 1) {
			return "<a href='javascript:void(0)'>[首页]</a>";
		} else {
			return "<a href='{$this->url}1'>[首页]</a>";
		}
	}

	private function getLast()
	{
		if ($this->current == $this->pagenum) {
			return  "<a href='javascript:void(0)'>[末页]</a>";
		} else {
			return  "<a href='{$this->url}{$this->pagenum}'>[末页]</a>";
		}
		
	}

	private function getAllPage()
	{
		$allPage = '';
		if ($this->pagenum <= $this->page) {
			for ($i = 1; $i <= $this->pagenum ; $i++) { 
				if ($this->current == $i) {
					$allPage .=  "<a href='javascript:void(0)'>{$this->current}</a>";
				} else {
					$allPage .=  "<a href='{$this->url}{$i}'>{$i}</a>";
				}
			}
		} else {
			if (($this->current-floor($this->page/2)) < 1) {
				for ($i = 1; $i <= $this->page ; $i++) { 
					if ($this->current == $i) {
						$allPage .=  "<a href='javascript:void(0)'>{$this->current}</a>";
					} else {
						$allPage .=  "<a href='{$this->url}{$i}'>{$i}</a>";
					}
				}
			}else if(($this->current+floor($this->page/2)) >= $this->pagenum) {
				for ($i = $this->pagenum - $this->page + 1; $i <= $this->pagenum ; $i++) { 
					if ($this->current == $i) {
						$allPage .=  "<a href='javascript:void(0)'>{$this->current}</a>";
					} else {
						$allPage .=  "<a href='{$this->url}{$i}'>{$i}</a>";
					}
				}
			}else {
				for ($i = $this->current - floor($this->page/2); $i <= ($this->current + $this->page -1 - floor($this->page/2)) ; $i++) { 
					if ($this->current == $i) {
						$allPage .=  "<a href='javascript:void(0)'>{$this->current}</a>";
					} else {
						$allPage .=  "<a href='{$this->url}{$i}'>{$i}</a>";
					}
				}
			}
		}
		return $allPage;
	}

	private function getPrev()
	{
		if ($this->current == 1) {
			return  "<a href='javascript:void(0)'>[上一页]</a>";
		} else {
			return  "<a href='{$this->url}".($this->current - 1)."'>[上一页]</a>";
		}
		
	}

	private function getNext()
	{
		if ($this->current == $this->pagenum) {
			return  "<a href='javascript:void(0)'>[下一页]</a>";
		} else {
			return  "<a href='{$this->url}" . ($this->current + 1)."'>[下一页]</a>";
		}
	}

	/**
	 * getPage方法，得到分页信息
	 * @access public
	 * @return string 分页信息字符串
	 */
	public function showPage()
	{
		if ($this->pagenum > 0) {
			return "共有 {$this->total} 条记录,每页显示 {$this->pagesize} 条记录， 当前为 {$this->current}/{$this->pagenum} {$this->first} {$this->prev} {$this->allPage} {$this->next} {$this->last}";
		} else {
			return "共有 {$this->total} 条记录";
		}
	}
}

//使用:
/*
$pager = new Pager(总的记录数, 显示页数, 每页记录数, 当前页数, 'php脚本index.php', array(参数
    'a' => 'index',
    'c' => 'product',
));

$pagerHtml = $pager->showPage();
*/