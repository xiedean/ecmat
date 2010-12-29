<?php

/**
 * article_groups table
 *
 */
class ArticleGroups extends Moore_Db_Table
{
	protected $_name = 'article_groups';
	protected $_idField = 'article_group_id';

	public function getAllQuery( $where=null,$order=null,$limit=null )
	{
		$query = $this->select()
		              ->from( $this->_name,array('*','id'=>'article_group_id'));
		
		if($where){
			$query->where($where);
		}
		if($order){
			$query->order($order);
		}
		else{
			$query->order($this->_idField.' DESC');
		}
		if($limit){
			$query->limit($limit);
		}
		return $query;
	}

	public function load($id)
	{
		if( !$id ){
			return false;
		}
		$row = $this->fetchRow("$this->_idField = '$id'");
		if($row){
			$row = $row->toArray();
		}
		return $row;
	}
	
	public function getOptions()
	{
		$rows = $this->fetchAll(null,"convert(article_group_name using gbk)");
		$result = array(""=>"請選擇");
		if($rows){
			foreach($rows as $row) {
				$result[$row->article_group_id] = $row->article_group_name;
			}
		}
		return $result;
	}


}