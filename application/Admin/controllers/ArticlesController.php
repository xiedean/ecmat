<?php

/**
 * Articles Controller
 *
 * @author
 * @version
 */

class Admin_ArticlesController extends Main_AdminController
{

	public function dataList()
	{
		$this->view->columns = array(

			array(
				'colName'  => 'title',
				'colType'  => 'text',
				'colTitle' => '文章標題'
			),

			array(
			    'colName'  => 'author',
			    'colType'  => 'text',
				'colTitle' => '作者'
			),

			array(
			    'colName'  => 'class_name',
			    'colType'  => 'text',
				'colTitle' => '欄目'
			),
			
			array(
			    'colName'  => 'article_group_name',
			    'colType'  => 'text',
				'colTitle' => '文章組'
			),

			array(
			    'colName'  => 'modify_time',
			    'colType'  => 'text',
				'colTitle' => '發佈時間'
			),
			array(
			   'colName'   => 'status',
			   'colType'   => 'enum',
			   'colTitle'  => '狀態',
			   'colValues' => array('0'=>'審核中','1'=>'已發布')
			)
		);
	}

	public function indexAction()
	{
		$this->view->navArray = array('title' => '文章列表');
		$this->view->itemName = '文章';
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
			$where = "a.title like '%$keyword%'";
		}
		if( $by && $order){
			$order_spec = array($by." ".$order);
		}
		else {
			$order_spec = array("article_id DESC");
		}
		$this->view->by = $by?$by:'article_id';
		$this->view->order = $order?$order:'asc';

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$this->view->site_id = $site_id = $input->getEscaped('site')?$input->getEscaped('site'):1;
		$table = new Articles();
		$query = $table->getAllQuery( $site_id,$where,$order_spec);

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
		                              '文章列表'=>$this->view->url(array('controller'=>'articles','module'=>'admin'),null,true),
		                              'title' => '添加新文章'
		                             );
		$this->view->itemName = '文章';
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
		        'site'     => 'Digits'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$id = $input->getEscaped('id');
		$this->view->site_id = $site_id = $input->getEscaped('site');
		$newsImagePath = Zend_Registry::get('newsImagePath');
		if($id){
			$table = new Articles();
			$row = $table->getArticleById( $id );
			$this->view->form = $form = new ArticleEditForm( $site_id );
			$form->setDefaults( $row );
			$this->view->navArray['title']= "修改文章信息";
			$str = "<a href=\"{$this->view->baseUrl()}{$newsImagePath}{$row['image']}\" target=\"_blank\" >查看圖片</a>";
			$form->image->setDescription( $str );
			$this->view->addPage = $id;
			$articlePages = new ArticlePages();
			$this->view->pages = $articlePages->getArticlePageById($id);
		}
		else{
			$this->view->form = $form = new ArticleAddForm($site_id);
			$name = $this->loggedInUser->name ? $this->loggedInUser->name : $this->loggedInUser->username;
			$form->setDefault('author', $name);
		}

		if( !$this->_request->isPost() ){
			return;
		}
		if( !$form->isValid($this->_request->getPost()) ){
			$errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不對";
				$this->view->errorId = $key;
				return;
			}
		}

	    if( $form->news_image->isUploaded() ){
		     //获取上传的文件名
            $tmp_filename_str = $form->news_image->getFileName();
            $tmp_filename_str = strtolower($tmp_filename_str);
            while(file_exists(iconv("UTF-8","gb2312",$tmp_filename_str)))  {
                $pos = strrpos($tmp_filename_str, ".");
                if (!$pos) {
                    $this->view->notice = "上傳失敗，圖片文件格式不對";
                    return;
                }
                $fileName = substr($tmp_filename_str, 0, $pos);//获得文件主名
                $fileNamelast=substr(strrchr($tmp_filename_str, '.'), 1);//获得文件扩展名
                $fileName = $fileName . time();//获得新命名

                $tmp_filename_str = $fileName.'.'.$fileNamelast;
              //  $tmp_newfilename_str  = str_replace($path,"",$tmp_filename_str);
                $form->news_image->addFilter('Rename', $tmp_filename_str);
            }
		}

		if(!$form->news_image->receive() ) {
			$this->view->notice = "圖片文件上傳失敗";
			return;
		}
	    
		$data = parent::formatData( $form->getValues());
		if($form->news_image->getValue()){
		    $data['image'] = $form->news_image->getValue();
		    if(!empty($form->image)) {
			    $str = "<a href=\"{$this->view->baseUrl()}{$newsImagePath}{$data['image']}\" target=\"_blank\" >查看圖片</a>";
				$form->image->setDescription( $str );
		    }
		}

		$table = new Articles();
		if($id){
			$result = $table->update($data,"article_id = '$id'");
			if( $result ){
			    $table = new Articles();
			    $row = $table->getArticleById( $id );
			    $form->setDefaults( $row );
				$this->view->notice = "修改成功";
				return;
			}
		}
		else{
			$result = $table->insert($data);
		    if( $result ){
				$noticeSession = new Zend_Session_Namespace('notice');
		        $noticeSession->notice = "添加成功";
				$this->_redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/index/site/$site_id");
				return;
			}
		}
	}

	public function deleteAction()
	{
		$this->view->navArray = array(
		                              '文章列表'=>$this->view->url(array('controller'=>'articles','module'=>'admin'),null,true),
		                              'title' => '刪除文章'
		                             );
		$this->view->itemName = '文章';
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
	    		'submit-y' => 'notEmpty',
	    		'submit-n' => 'notEmpty',
		        'site'     => 'Digits'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$this->view->site_id = $site_id = $input->getEscaped('site');
		$this->view->id = $id = $input->getEscaped('id');
		$table = new Articles();
		if($this->_request->isPost()){

			if( $input->getEscaped('submit-y')){
				$result = $table->delete("article_id = '$id'");
				if($result){
					$this->_redirect('/admin/articles/index/site/'.$site_id);
				}
				else{
					$this->view->notice = "删除失败 ";
				}
			}
			else{
				$this->_redirect('/admin/articles/index/site/'.$site_id);
			}
		}
		$row = $table->getArticleById( $id );
		$this->view->deleteName = $row['title'];
	}

	public function addpageAction()
	{
	    $this->view->navArray = array(
		                              '文章列表'=>$this->view->url(array('controller'=>'articles','module'=>'admin'),null,true),
		                              'title' => '添加新文章頁'
		                             );
		$this->view->itemName = '文章';
	    // filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
		        'page' => 'Digits',
		        'site' => 'Digits'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid() ){
			$this->view->error = "參數錯誤";
			return;
		}
		$id = $input->getEscaped('id');
		if(!$id ){
		    $this->view->error = "參數錯誤";
		    return false;
		}
		$page = $input->getEscaped('page');
		$site = $input->getEscaped('site');
		$articles = new Articles();
		$this->view->article = $articles->getArticleById($id);
		$this->view->form = $form = new ArticlePageAddForm();
		$this->view->page = $page;
		$table = new ArticlePages();

		if($page) {
		    $row = $table->getPage($id, $page);
            if ($row) {
                $form->setDefaults($row);
            }
        }
        else {
            $rows = $table->getArticlePageById($id);
            if($rows)
                $article_page = $rows[count($rows)-1]['article_page']+1;
            else
                $article_page = 2;
            $arr = array('article_id'=>$id, 'article_page'=>$article_page);
            $form->setDefaults($arr);
        }
        if(!$this->_request->isPost()) {
            return ;
        }
	    if( !$form->isValid($this->_request->getPost()) ){
			$errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不對";
				$this->view->errorId = $key;
				return;
			}
		}
		$data = parent::formatData( $form->getValues());

		if($page) {
		    $where = "article_id = '$id' and article_page ='$page'";
		    $result = $table->update($data, $where);
		    $article_page = $page;
		}
		else {
		    $data['article_id'] = $id;
		    $result = $table->insert($data);

		}
		$noticeSession = new Zend_Session_Namespace('notice');
		if($result)
		    $noticeSession->notice = "第 $article_page 頁發布成功";
		else
		    $noticeSession->notice = "第 $article_page 頁發布失敗";

		$this->_redirect('/admin/articles/edit/id/'.$id.'/site/'.$site);
	}

	public function deletepageAction()
	{
		$this->view->navArray = array(
		                              '文章列表'=>$this->view->url(array('controller'=>'articles','module'=>'admin'),null,true),
		                              'title' => '刪除文章分页'
		                             );
		$this->view->itemName = '文章';
		// filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'id' => 'Digits',
	    		'submit-y' => 'notEmpty',
	    		'submit-n' => 'notEmpty',
		        'site'     => 'Digits',
		        'page'     => 'Digits'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$this->view->site_id = $site_id = $input->getEscaped('site');
		$this->view->id = $id = $input->getEscaped('id');
		$page = $input->getEscaped('page');
		$table = new ArticlePages();
		if($this->_request->isPost()){
			if( $input->getEscaped('submit-y')){
				$result = $table->delete("article_id = '$id' && article_page = '$page'");
				if($result){
				    $noticeSession = new Zend_Session_Namespace('notice');
				    $noticeSession->notice = "刪除第 $page 頁成功";
					$this->_redirect('/admin/articles/edit/id/'.$id.'/site/'.$site_id);
				}
				else{
					$this->view->notice = "刪除失敗 ";
				}
			}
			else{
				$this->_redirect('/admin/articles/edit/id/'.$id.'/site/'.$site_id);
			}
		}
		$this->view->deleteName = "第 $page 頁";
	}
}
