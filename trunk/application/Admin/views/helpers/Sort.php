<?php
class Zend_View_Helper_Sort
{
	public function sort ($colName, $by, $order)
	{
		if ($by == $colName) { 
			switch (strtolower($order)) { 
				case '': return "sort"; 
				case 'desc': return 'desc'; 
				case 'asc': return 'asc'; 
				default: return "sort";
			}
		} else {
			return "sort";
		}
	}
}