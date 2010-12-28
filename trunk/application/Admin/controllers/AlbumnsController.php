<?php

/**
 * AlbumnsController
 *
 * @author
 * @version
 */

class Admin_AlbumnsController extends Main_AdminController
{
    public function preDispatch()
    {
        $this->view->itemName = '相冊';
        if( !in_array($this->_request->getActionName(),array('upload','thumbnail'))){
            if( !in_array($this->role, array('administrator','editor'))){
        //        $this->_redirect('admin/auth');
            }
        }
    }
	public function indexAction()
	{
		$this->view->navArray = array('title' => '相冊');
		$filter = new Filter();
		$input = $filter->filterValid( $this->_request->getParams() );
		if( !$input ){
			$this->view->error = "參數錯誤";
			return;
		}
		$currentPageNumber = $input->getEscaped('page')>0 ?$input->getEscaped('page'):1;
		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Albumns();
		$query = $table->getAllQuery(null,null,null);

		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($currentPageNumber)
				  ->setItemCountPerPage($pageSize)
        		  ->setPageRange($pageRange);

		$this->view->paginator = $paginator;
		$this->view->count = $paginator->getCurrentItemCount();
		$this->view->albumnPath = $registry->get("albumnPath");

	}

	public function editAction()
	{
		$this->view->navArray = array(
		                              '相册列表'=>$this->view->url(array('controller'=>'albumns','module'=>'admin'),null,true),
		                              'title' => '添加新相册'
		                             );
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
		        'string' => new Zend_Validate_Regex('/^[\s\S]+$/')
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$id = $input->getEscaped('id');
		if($id){
			$table = new Albumns();
			$row = $table->getAlbumn( $id );
			$this->view->form = $form = new AlbumnEditForm();
			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改相冊信息";

		}
		else{
			$this->view->form = $form = new AlbumnAddForm();
		}
		if( !$this->_request->isPost() ){
			return;
		}
		if( !$form->isValid($this->_request->getPost()) ){
			$errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不對";
        		return;
			}
		}
		$data = parent::formatData( $form->getValues());
		$table = new Albumns();
		if($id){
			$result = $table->update($data,"albumn_id = '$id'");
			if( $result ){
				$noticeSession = new Zend_Session_Namespace('notice');
		        $noticeSession->notice = "修改成功";
				$this->_redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}");
				return;
			}
		}
		else{
			$result = $table->insert($data);
		    if( $result ){
				$this->view->notice = "添加成功";
				return;
			}
		}
	}

	public function deleteAction()
	{
		$this->view->navArray = array(
		                              '相冊列表'=>$this->view->url(array('controller'=>'albumns','module'=>'admin'),null,true),
		                              'title' => '刪除相冊'
		                             );
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
	    		'submit-y' => 'notEmpty',
	    		'submit-n' => 'notEmpty',
		        'string' => new Zend_Validate_Regex('/^[\s\S]+$/')
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$this->view->id = $id = $input->getEscaped('id');
		$table = new Albumns();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->deleteAlbumn($id);

				if($result){
					$this->_redirect('/admin/albumns');
				}
				else{
					$this->view->notice = "冊除失败 ";
				}
			}
			else{
				$this->_redirect('/admin/albumns');
			}
		}
		$row = $table->getAlbumn( $id );
		$this->view->deleteName = $row['albumn_name'];

	}


	function photoAction()
    {
     	$imgSession = new Zend_Session_Namespace('img');
    	$params = $this->_request->getParams();
    	/*
    	 * check the owner if is the video's owner
    	 * or is the administrator .
    	 */
    	$albumn_id = $this->view->albumn_id = $params['albumn'];
    	$table = new Albumns();
    	$row = $table->getAlbumn($albumn_id);
    	$this->view->albumn_name = $row['albumn_name'];
    	$this->view->albumnPath = Zend_Registry::get("albumnPath");
       	$imgSession = new Zend_Session_Namespace('img');
       	$imgSession->file_info = array();     //clear the session thumbnail

       	$response = $this->getResponse();
		$response->insert('scripts',$this->view->render('albumns/uploadScript.phtml'));
		$this->view->navArray = array(
		                              '相冊列表' => $this->view->url(array('controller'=>'albumns','module'=>'admin'),null,true),
                                      $row['albumn_name'] => $this->view->url(array('controller'=>'albumns','module'=>'admin','action'=>'detail','albumn'=>$albumn_id),null,true),
		                              'title' => '上傳照片'
		                             );

    }

    function uploadAction()
    {
    	//set a new layout for thisAction
    	$layout = new Zend_Layout();
    	$layout->startMvc(array('layoutPath'=>'./application/Admin/views/blankLayouts'));
    	/* Note: This thumbnail creation script requires the GD PHP Extension.
		If GD is not installed correctly PHP does not render this page correctly
		and SWFUpload will get "stuck" never calling uploadSuccess or uploadError
	    */
	    // Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
	    $params = $this->_request->getParams();
	    $albumn_id = $params['albumn_id'];
	    $photo_name = $_FILES["Filedata"]["name"];
	    if (isset($params['sid'])) {
	    	session_id($params['sid']);
	    	// get the user authentication
	    	if($params['role'] == 'administrator') {
	    		$table = new Admins();
                $table->reLogin($params['id']);
            }
	    }
	    ini_set("html_errors", "0");

	    $imgSession = new Zend_Session_Namespace('img');

	    // Check the upload
	    if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
	    	echo "ERROR:invalid upload";
	    	exit(0);
	    }
	    // Get the image and create a thumbnail
	        //get infomation of iamge
             /*
              * 0 => width
              * 1 => height
              * 2 => 1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，8 = TIFF(motorola byte order)，9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM
              * 3 => height="yyy" width="xxx"
              */
	    $img_type = getimagesize($_FILES["Filedata"]["tmp_name"]);
	    switch($img_type[2]) {
	    	case 1 :
	    		$img = imagecreatefromgif($_FILES["Filedata"]["tmp_name"]);
	            if (!$img) {
	    	        echo "ERROR:could not create image handle ". $_FILES["Filedata"]["tmp_name"];
	     	        exit(0);
	            }
    	        break;
	    	case 2 :
	    		$img = imagecreatefromjpeg($_FILES["Filedata"]["tmp_name"]);
	            if (!$img) {
    		        echo "ERROR:could not create image handle ". $_FILES["Filedata"]["tmp_name"];
	    	        exit(0);
	            }
	            break;
	    	case 3 :
	    		$img = imagecreatefrompng($_FILES["Filedata"]["tmp_name"]);
	            if (!$img) {
	    	        echo "ERROR:could not create image handle ". $_FILES["Filedata"]["tmp_name"];
	    	        exit(0);
	            }
	            break;
	        default: exit(0);
	    }

	    $width = imageSX($img);
	    $height = imageSY($img);

	    if (!$width || !$height) {
	    	echo "ERROR:Invalid width or height";
	    	exit(0);
	    }

	    // Build the thumbnail
	    $target_width = 110;
    	$target_height = 110;
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
    	$new_img = ImageCreateTrueColor(110, 110);
    	$white = ImageColorAllocate($new_img,255,255,255);
    	if (!@imagefilledrectangle($new_img, 0, 0, $target_width-1, $target_height-1, $white)) {	// Fill the image black
      		echo "ERROR:Could not fill new image";
    		exit(0);
    	}
    	if (!@imagecopyresampled($new_img, $img, ($target_width-$new_width)/2, ($target_height-$new_height)/2, 0, 0, $new_width, $new_height, $width, $height)) {
	    	echo "ERROR:Could not resize image";
	    	exit(0);
    	}
    	if (!isset($imgSession->file_info)) {
    	    $imgSession->file_info = array();
	    }
  	    // Use a output buffering to load the image into a variable
  	    $registry = Zend_Registry::getInstance();
  	    $albumnPath = $registry->get("albumnPath");
  	    $albumnPath = substr($albumnPath,1);
  	    $albumn_id = $params['albumn_id'];

  	    $img_dir = $albumnPath.$albumn_id."/";   //slide floder
    	if(!is_dir($img_dir)) {
     	    mkdir($img_dir);
    	}
  	    $img_mini_dir = $albumnPath.$albumn_id."/thumbnail/";   // thumbnail floder
  	    if(!is_dir($img_mini_dir)) {
     	    mkdir($img_mini_dir);
    	}
    	//rename photo files
    	$type=substr(strrchr($photo_name, '.'), 1);// get file ext
    	$newName = "ps".md5(microtime(true)).".".$type;

    	ob_start();   // Turn on output buffering
    	switch($img_type[2]) {
	    	case 1 :
	    		imagegif($new_img,iconv("UTF-8","gb2312",$img_mini_dir.$newName));  //output to the thumbnail floder
	    		imagegif($new_img);    //output to the browser
	    		break;
	    	case 2 :
	    		imagejpeg($new_img,iconv("UTF-8","gb2312",$img_mini_dir.$newName),100);
	    		imagejpeg($new_img);
	    		break;
	    	case 3 :
	    		imagepng($new_img,iconv("UTF-8","gb2312",$img_mini_dir.$newName));
	    		imagepng($new_img);
	    		break;
    	}
    	$imagevariable = ob_get_contents();
	    ob_end_clean();

    	//save the file to local driver
	    move_uploaded_file($_FILES["Filedata"]["tmp_name"],iconv("UTF-8","gb2312",$img_dir.$newName));
	    $file_id = md5($newName + rand()*100000);
    	$imgSession->file_info[$file_id] = $imagevariable;
    	$table = new Photos();
    	$photo_name = str_replace(".".$type,"",$photo_name);
    	$arr = array('photo_name'=>$photo_name,'albumn_id'=>$albumn_id,'created'=>date("Y-m-d H:i:s"),'string'=>$newName);
    	$result = $table->insert($arr);

    	if($result)
    	   	echo "FILEID:" . $file_id;	// Return the file id to the script
    	
    }

    function thumbnailAction()
    {
    	//set a new layout for thisAction
    	$layout = new Zend_Layout();
    	$layout->startMvc(array('layoutPath'=>'./application/Admin/views/blankLayouts'));

    	$params = $this->_request->getParams();
    	$registry = Zend_Registry::getInstance();
    	$log = $registry->get("rootPath") ."\errors.log";
    	$f = fopen( $log, "w" );
    	fwrite( $f, "aaa" );
        fclose( $f );
    	// Work around the Flash Player Cookie Bug
    	if (isset($params['sid'])) {
	    	session_id($params['sid']);
	    	if($params['role'] == 'administrator') {
	    		$table = new Admins();
                $table->reLogin($params['admin_id']);
            }
    	}
    	$imgSession = new Zend_Session_Namespace('img');
    	// This script accepts an ID and looks in the user's session for stored thumbnail data.
	    // It then streams the data to the browser as an image

	    $image_id = isset($params['id']) ? $params['id'] : false;

	    if ($image_id === false) {
		    header("HTTP/1.1 500 Internal Server Error");
		    echo "No ID";
		    exit(0);
	    }

	    if (!is_array($imgSession->file_info) || !isset($imgSession->file_info[$image_id])) {
	    	header("HTTP/1.1 404 Not found");
	    	exit(0);
	    }

	    header("Content-type: image/jpeg") ;
	    header("Content-Length: ".strlen($imgSession->file_info[$image_id]));
	    echo $imgSession->file_info[$image_id]; 
	    exit(0);
    }

    function detailAction()
    {

        $filter = new Filter();
		$input = $filter->filterValid( $this->_request->getParams() );
		if( !$input ){
			$this->view->error = "參數錯誤";
			return;
		}
		$id = $input->getEscaped('albumn');
		$albumns = new Albumns();
		$row = $albumns->getAlbumn($id);
		if(!$row){
		    $this->view->error = "相册不存在";
			return;
		}
		$this->view->navArray = array(
		                              '相册列表'=>$this->view->url(array('controller'=>'albumns','module'=>'admin'),null,true),
		                              'title' => $row['albumn_name']
		                             );
		$table = new Photos();
		$currentPageNumber = $input->getEscaped('page')>0 ?$input->getEscaped('page'):1;
		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');
		$query = $table->getAllQuery( "albumn_id = '$id'" );

		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($currentPageNumber)
				  ->setItemCountPerPage($pageSize)
        		  ->setPageRange($pageRange);

		$this->view->paginator = $paginator;
		$this->view->count = $paginator->getCurrentItemCount();
		$this->view->albumn_id = $id;
		$this->view->albumnPath = $registry->get("albumnPath");


    }


    function delphotoAction()
    {
        //set a new layout for thisAction
    	$layout = new Zend_Layout();
    	$layout->startMvc(array('layoutPath'=>'./application/Admin/views/blankLayouts'));

        $filter = new Filter();
		$input = $filter->filterValid( $this->_request->getParams() );
		if( !$input ){
			$this->view->error = "参数错误";
			return;
		}
		$id = $input->getEscaped('id');
		$table = new Photos();
		if($table->deletePhoto($id)){
		    echo "success";
		    die();
		}
		echo "false";
		die();

    }

    public function viewAction()
    {
        // filter input
		$filters = array('*' => array('StringTrim','HtmlEntities'));
		$validators = array(
	    		'photo' => 'Digits',
		        'string' => new Zend_Validate_Regex('/^[\s\S]+$/')
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$id = $this->view->photoId = $input->getEscaped('photo');
		$table = new Photos();
		$this->view->data = $row = $table->getPhoto($id);
		if(!$row){
		    $this->view->error = "照片不存在";
			return;
		}
		$this->view->albumnPath = Zend_Registry::get("albumnPath");
		$albumns = new Albumns();
		$albumn = $albumns->getAlbumn($row['albumn_id']);
		$this->view->navArray = array(
		                              '相冊列表' => $this->view->url(array('controller'=>'albumns','module'=>'admin'),null,true),
                                      $albumn['albumn_name'] => $this->view->url(array('controller'=>'albumns','module'=>'admin','action'=>'detail','albumn'=>$row['albumn_id']),null,true),
		                              'title' => "瀏覽照片"
		                             );
    }

    public function prephotoAction()
    {
        if(!$this->_request->isPost()){
            return false;
        }
        //set a new layout for thisAction
    	$layout = new Zend_Layout();
    	$layout->startMvc(array('layoutPath'=>'./application/Admin/views/blankLayouts'));
    	$id = $this->_request->getParam('id',0);
    	if(!$id){
    	    return false;
    	}
    	$table = new Photos();
    	$row = $table->getPrePhoto( $id );
    	if($row){
    	    echo Zend_Json::encode($row);
    	}
    	else{
    	    echo "{ photo_id: 'last'}";
    	}
    }

    public function nextphotoAction()
    {
        if(!$this->_request->isPost()){
            return false;
        }
        //set a new layout for thisAction
    	$layout = new Zend_Layout();
    	$layout->startMvc(array('layoutPath'=>'./application/Admin/views/blankLayouts'));
    	$id = $this->_request->getParam('id',0);
    	if(!$id){
    	    return false;
    	}
    	$table = new Photos();
    	$row = $table->getNextPhoto( $id );
    	if($row){
    	    echo Zend_Json::encode($row);
    	}
    	else{
    	    echo "{ photo_id: 'last'}";
    	}

    }
}
