<?php 
class Myfunctions
{
	function __construct()
	{
		
	}
	
	/**
	 * order a array ,from small to big
	 *
	 * @param array $array
	 * @return array
	 */
    function orderArray($array)
	{
		$n = count($array);
		for($a=0; $a < $n; $a++) {
			for($b=$n-1; $b > $a; $b--) {
				if($array[$b] < $array[$b-1]) {
					$c = $array[$b-1];
					$array[$b-1] = $array[$b];
					$array[$b] = $c;
					
				}
			}
		}
		return $array;
	}
	
	/**
	 * upload file through <input type="file" name=$input_name>, 
	 *
	 * @param string $upload_path   the path saved the upload files
	 * @param string $input_name    
	 * @param array $type   type of upload files limited
	 * @return bool
	 */
	function getUpoadfile($upload_path,$input_name,$type=null)
	{
		$flag = false;
		$pos = strrpos($_FILES[$input_name]['name'], '.');
		$file_name = substr($_FILES[$input_name]['name'], 0, $pos);
		$ext = substr($_FILES[$input_name]['name'], $pos + 1);
		if($type) {
			$ext = strtolower($ext);
			if(!in_array($ext,$type)){
				return "2";
			}
		}
		if(file_exists($upload_path.$_FILES[$input_name]['name']))	{
			$seq = 1;
			while (file_exists($upload_path.$file_name."(".$seq.")".".".$ext))
			{
				$seq=$seq+1;
			}
		}
		$file_name = $file_name.(empty($seq)?"":"(".$seq.")").".".$ext;
		if($input_name == "thumbnail") {
			$this->createThumbnail($_FILES[$input_name]['tmp_name'],$file_name,"thumbnail_mini/",120,97);
		}
		if(!is_dir($upload_path)) {
			mkdir($upload_path,0777);
		}
		$flag = move_uploaded_file($_FILES[$input_name]['tmp_name'],$upload_path.$file_name);  
		if($flag){
			return $file_name;
		}
		else return false;
	}
	
	/**
	 * create a thumbnail to a image file.
	 *
	 * @param string $file    the image file, uri or url
	 * @param string $file_name    thumbnail name
	 * @param string $dir         the path of thumbnail saved
	 * @param int $width_dest
	 * @param int $height_dest
	 */
	function createThumbnail($file,$file_name,$dir,$width_dest,$height_dest)
	{
		$img = imagecreatefromjpeg($file);
			$width = imagesx($img);
			$height = imagesy($img);
			if (!$width || !$height) {
				echo "ERROR:Invalid width or height";
				exit(0);
			}
			// Build the thumbnail
			$target_width = $width_dest;
			$target_height = $height_dest;
			$target_ratio = $target_width / $target_height;
			$img_ratio = $width / $height;
			if ($target_ratio > $img_ratio) {
				$new_height = $target_height;
				$new_width = $img_ratio * $target_height;
			} else {
				$new_height = $target_width / $img_ratio;
				$new_width = $target_width;
			}
			if ($new_height > $target_height) {
				$new_height = $target_height;
			}
			if ($new_width > $target_width) {
				$new_height = $target_width;
			}
			$new_img = ImageCreateTrueColor($target_width,$target_height);
			if (!@imagefilledrectangle($new_img, 0, 0, $target_width-1, $target_height-1, 0)) {	// Fill the image black
				echo "ERROR:Could not fill new image";
				exit(0);
			}
			if (!@imagecopyresampled($new_img, $img, ($target_width-$new_width)/2, ($target_height-$new_height)/2, 0, 0, $new_width, $new_height, $width, $height)) {
				echo "ERROR:Could not resize image";
				exit(0);
			}
			$img_mini_dir = $dir;   // mini thumbnail floder
			if(!is_dir($img_mini_dir)) {
				mkdir($img_mini_dir,0777);
			}
			imagejpeg($new_img,$img_mini_dir.$file_name,100);   // save the mini thumbnail
	}

}