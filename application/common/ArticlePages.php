<?php

/**
 * Articles
 *
 * @author Administrator
 * @version
 */


class ArticlePages extends Moore_Db_Table
{
	/**
	 * The default table name
	 */
	protected $_name = 'article_pages';

	public function getArticlePageById( $id )
	{
		if( !$id ){
			return false;
		}

		$rows = $this->fetchAll("article_id = '$id'","article_page");
		if($rows->count() > 0){
			$rows = $rows->toArray();
			return $rows;
		}
		return false;
	}

	public function getPage($id, $page)
	{
	    $where = "article_id = '$id' and article_page = '$page'";
	    $row = $this->fetchRow($where);
	    if($row) {
	        $row = $row->toArray();
	    }
	    return $row;
	}


}
