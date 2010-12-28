<?php
class PhotosController extends Main_CommonController
{
    public function preDispatch()
    {
        $this->view->selectItemId = "idsPhotos";
        $this->view->site = 2;
    }
    public function indexAction()
	{
		$this->view->headerName = "相册";
		$filter = new Filter();
		$input = $filter->filterValid( $this->_request->getParams() );
		if( !$input ){
			$this->view->error = "参数错误";
			return;
		}
		$currentPageNumber = $input->getEscaped('page')>0 ?$input->getEscaped('page'):1;
		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Albumns();
		$query = $table->getAllQuery( );

		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($currentPageNumber)
				  ->setItemCountPerPage($pageSize)
        		  ->setPageRange($pageRange);

		$this->view->paginator = $paginator;
		$this->view->count = $paginator->getCurrentItemCount();
		$this->view->albumnPath = $registry->get("albumnPath");

	}


    function detailAction()
    {

        $filter = new Filter();
		$input = $filter->filterValid( $this->_request->getParams() );
		if( !$input ){
			$this->view->error = "参数错误";
			return;
		}
		$id = $input->getEscaped('albumn');
		$albumns = new Albumns();
		$row = $albumns->getAlbumn($id);
		if(!$row){
		    $this->view->error = "相册不存在";
			return;
		}

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
		$this->view->navArray = array(
		                              '相册列表'=>$this->view->url(array('controller'=>'photos'),null,true),
		                              'title' => $row['albumn_name']
		                             );


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
			$this->view->error = "参数错误";
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
		                              '相册列表' => $this->view->url(array('controller'=>'photos'),null,true),
                                      $albumn['albumn_name'] => $this->view->url(array('controller'=>'photos','action'=>'detail','albumn'=>$row['albumn_id']),null,true),
		                              'title' => "浏览照片"
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