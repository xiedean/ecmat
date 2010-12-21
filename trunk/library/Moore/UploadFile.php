<?php

class Moore_UploadFile
{
	static $instance = false;
	public  $ext;

	protected  function __construct()
	{
	}

	public static function getInstance()
	{
		if (!Moore_UploadFile::$instance) {
			Moore_UploadFile::$instance = new Moore_UploadFile;
		}
		return Moore_UploadFile::$instance;
	}

	public function upload($file,$destination,$allownFileTypes = array(),$oldFileName = null)
	{
		$seq = "";
		$fileName = "";
		$ext = "";
		$flag = false;

		if(isset($file['name'])&&(!empty($file['name']))) {
			$pos = strrpos($file['name'], '.');
			$fileName = substr($file['name'], 0, $pos);
			$ext = substr($file['name'], $pos + 1);
			$this->ext = $ext;

			if (!in_array($ext,$allownFileTypes))
				return false;
			if(isset($oldFileName)&&(!empty($oldFileName))
				&& file_exists($destination.$oldFileName)) {
				unlink($destination.$oldFileName);
			}

		//	if(file_exists($destination.$file['name'])) {
			$seq = time();
			while (file_exists($destination.$fileName."(".$seq.").".$ext)) {
				$seq ++;
			}
		//	}
		//	$fileName = $fileName.(empty($seq)?"":"_".$seq).".".$ext;
			$fileName = $fileName."_".$seq.".".$ext;
			$flag = move_uploaded_file($file['tmp_name'],$destination.$fileName);
		}
		return $fileName;
	}

	public function getFileType()
	{
		return $this->ext;
	}
}