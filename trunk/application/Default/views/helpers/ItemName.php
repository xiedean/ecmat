<?php
class Zend_View_Helper_ItemName
{
	/**
	 * get the item name
	 *
	 * @return string
	 */
	function itemName($id )
	{
		switch ($id){
		    case 1:
		        $itemName = "活動公告";
		        break;
		    case 2:
		        $itemName = "今日看板";
		        break;
		    case 3:
		        $itemName = "明日活動";
		        break;
		    default:
		        return false;
		}
		return $itemName;
	}
}
?>