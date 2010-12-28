<?php
class Zend_View_Helper_Utf8substr
{
    public $view;

    function utf8substr ($str, $position=null, $length=null)
    {
        if(!$position){
            $position = 0;
        }
        if(!$length){
            $length = $this->view->utf8strlen($str);
        }
        $str = str_replace("&nbsp;","",$str);
        $str = str_replace("&ldquo;","“",$str);
        $str = str_replace("&rdquo;","”",$str);
        $str = str_replace("&hellip;","…",$str);
        $filter = new Zend_Filter_StripTags();
        $str = $filter->filter(trim($str));
        $str = trim($str);
        $start_position = strlen($str);
        $start_byte = 0;
        $end_position = strlen($str);
        $count = 0;
        for ($i = 0; $i < strlen($str); $i ++) {
            if ($count >= $position && $start_position > $i) {
                $start_position = $i;
                $start_byte = $count;
            }
            if (($count - $start_byte) >= $length) {
                $end_position = $i;
                break;
            }
            $value = ord($str[$i]);
            if ($value > 127) {
    //            $count ++;
                if ($value >= 192 && $value <= 223)
                    $i ++;
                elseif ($value >= 224 && $value <= 239)
                    $i = $i + 2;
                elseif ($value >= 240 && $value <= 247)
                    $i = $i + 3;
                else
                    die('Not a UTF-8 compatible string');
            }
            $count ++;
        }
        return (substr($str, $start_position, $end_position - $start_position));
    }

    /**
	 * Sets the view field
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}