<?php
class Zend_View_Helper_GetOrder
{
	public function getOrder($colName, $by, $order)
	{
		if ($by == $colName) { 		
			switch (strtolower($order)) { 
				case 'desc': return "asc"; 
						 break; 
				case 'asc': return 'desc'; 
						 break; 
				case '': return 'desc'; 
						 break;
				default: return 'desc';
			}
		} else {
			return 'desc';
		}
	}
}