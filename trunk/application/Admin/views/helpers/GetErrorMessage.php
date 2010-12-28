<?php
class Zend_View_Helper_GetErrorMessage
{
	function getErrorMessage($error)
	{
		if( isset($error) ) {	
			$errorMessages = ""; 
			foreach ($error as $errorMessage) {
				if (isset($errorMessage) && !empty($errorMessage))
					$errorMessages = $errorMessages.$errorMessage.'<br>';
			}
			if (isset($errorMessages))
				return '<br><div class="error-inline">'.$errorMessages.'</div>';
		}
	}
}