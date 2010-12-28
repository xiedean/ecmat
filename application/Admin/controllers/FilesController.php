<?php

/**
 * FilesController
 *
 * @author
 * @version
 */

class Admin_FilesController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'file_name',
				'colType'  => 'text',
				'colTitle' => '文件名'
			),

			array(
				'colName'  => 'created',
				'colType'  => 'text',
				'colTitle' => '發佈時間'
			),
			array(
			    'colName'  => 'site_name',
			    'colType'  => 'text',
			    'colTitle' => '所屬網站'
			),
			array(
			   'colName'   => 'status',
			   'colType'   => 'enum',
			   'colTitle'  => '狀態',
			   'colValues' => array('0'=>'不開放','1'=>'開放')
			)

		);
	}

	public function indexAction()
	{
		$this->view->navArray = array('title' => '文件列表');
		$this->view->itemName = '文件';
		$filter = new Filter();
		$input = $filter->filterValid( $this->_request->getParams() );
		if( !$input ){
			$this->view->error = "參數錯誤";
			return;
		}
		$this->view->keyword = $keyword = $input->getUnescaped('keyword');
		$order = $input->getEscaped('order');
		$by = $input->getEscaped('by');
		$currentPageNumber = $input->getEscaped('page')>0 ?$input->getEscaped('page'):1;
		$where = null;
		if($keyword){
			$where = "file_name like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array($by." ".$order);
		}
		else {
			$order_spec = array("file_id DESC");
		}
		$this->view->by = $by?$by:'file_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Files();
		$query = $table->getAllQuery( $where,$order_spec);

		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($currentPageNumber)
				  ->setItemCountPerPage($pageSize)
        		  ->setPageRange($pageRange);

		$this->view->paginator = $paginator;
		$this->view->count = $paginator->getCurrentItemCount();
		$this->dataList();
	}

	public function editAction()
	{
		$this->view->navArray = array(
		                              '文件列表'=>$this->view->url(array('controller'=>'files','module'=>'admin'),null,true),
		                              'title' => '添加新文件'
		                             );
		$this->view->itemName = '文件';
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$id = $input->getEscaped('id');
		$table = new Files();

		if($id){
			$row = $table->getFileById( $id );
			$this->view->form = $form = new FileEditForm();
			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改文件信息";
			$this->view->uploadPath = Zend_Registry::get('uploadPath');
		}
		else{
			$this->view->form = $form = new FileAddForm();
		}

		if( !$this->_request->isPost() ){
			return;
		}
		if( !$form->isValid($this->_request->getPost()) ){
			$errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = "上傳失敗，".$form->getElement($key)->getLabel()." 格式不對";
        		return;
			}
		}
		if( $table->checkFileName($form->file_name->getValue()) && (!$id)){
		    $this->view->notice = "上傳失敗，文件名 " .$form->file_name->getValue()." 已經存在";
		    return;
		}

		if( $form->myfile->isUploaded() ){
		     //获取上传的文件名
            $tmp_filename_str = $form->myfile->getFileName();
            $pos = strrpos($tmp_filename_str, "\\");
            $symbol = "\\";
            if (!$pos) {
                $pos = strrpos($tmp_filename_str, "/");
                $symbol = "/";
                if(!$pos) {
                    $this->view->notice = "上傳失敗，文件格式不對";
                    return;
                }
            }
            $fileName = substr($tmp_filename_str, 0, $pos);//获得文件主名
            $fileNamelast = substr(strrchr($tmp_filename_str, '.'), 1);//获得文件扩展名
            $fileName = $fileName .$symbol. md5(microtime(true));//获得新命名
            $tmp_filename_str = $fileName.'.'.$fileNamelast;
            $form->myfile->addFilter('Rename', $tmp_filename_str);
		}

		if(!$form->myfile->receive() ) {
			$this->view->notice = "文件上传失败";
			return;
		}

		$data = parent::formatData( $form->getValues() );
		if($form->myfile->getValue())
		    $data['file_name_src'] = $form->myfile->getValue() ;
		$data['file_name'] = $form->getValue('file_name')? $form->getValue('file_name'):$form->myfile->getFileName();

		if($id){
			$result = $table->update($data,"file_id = '$id'");
			if( $result ){
				$this->view->notice = "修改成功";
				$row = $table->getFileById( $id );
			    $this->view->form = $form = new FileEditForm();
			    $form->setDefaults( $row );
				return;
			}
		}
		else{
			$result = $table->insert($data);
		    if( $result ){
		        $noticeSession = new Zend_Session_Namespace('notice');
		        $noticeSession->notice = "添加成功";
				$this->_redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}");
				return;
			}
		}
	}

	public function deleteAction()
	{
		$this->view->navArray = array(
		                              '文件列表'=>$this->view->url(array('controller'=>'files','module'=>'admin'),null,true),
		                              'title' => '刪除文件'
		                             );
		$this->view->itemName = '文件';
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
	    		'submit-y' => 'notEmpty',
	    		'submit-n' => 'notEmpty'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$this->view->id = $id = $input->getEscaped('id');
		$table = new Files();

		$row = $table->getFileById( $id );
		$this->view->deleteName = $row['file_name'];

		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("file_id = '$id'");
				if($result){
					$registry = Zend_Registry::getInstance();
					$uploadPath = $registry->get('uploadPath');
					unlink($uploadPath."/".$row['file_name_src']);
					$this->_redirect('/admin/files');
				}
				else{
					$this->view->notice = "刪除失敗";
				}
			}
			else{
				$this->_redirect('/admin/files');
			}
		}

	}

}
