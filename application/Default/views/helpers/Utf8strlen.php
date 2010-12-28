<?php
class Zend_View_Helper_Utf8strlen
{
    function utf8strlen ($str)
    {
        $str = str_replace("&nbsp;","",$str);
        $str = str_replace("&ldquo;","“",$str);
        $str = str_replace("&rdquo;","”",$str);
        $str = str_replace("&hellip;","…",$str);
        $filter = new Zend_Filter_StripTags();
        $str = $filter->filter(trim($str));
        $str = trim($str);
        $count = 0;
        for ($i = 0; $i < strlen($str); $i ++) {
            $value = ord($str[$i]);
            if ($value > 127) {
      //          $count ++;
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
        return $count;
    }
}