<?php
class NoticeController extends Main_CommonController
{
    public function indexAction()
    {
        $filter = new Filter();
        $input = $filter->filterValid($this->_request->getParams());
        if( !$input ){
            $this->view->error = "参数错误";
            return;
        }
        $id = $input->getEscaped('id');
        if( !$id ){
            $this->view->error = "参数错误";
            return;
        }
        $this->view->itemId = $id;
        $table = new Notices();
        $where = "status = '1' and belong = '$id'";
		$currentPageNumber = $input->getEscaped('page')>0 ?$input->getEscaped('page'):1;
		$order_spec = array("notice_id DESC");

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$query = $table->getAllQuery( $where,$order_spec);

		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($currentPageNumber)
				  ->setItemCountPerPage($pageSize)
        		  ->setPageRange($pageRange);

		$this->view->paginator = $paginator;
		$this->view->count = $paginator->getCurrentItemCount();
    }

    public function detailAction()
    {
        $filter = new Filter();
        $input = $filter->filterValid($this->_request->getParams());
        if( !$input ){
            $this->view->error = "参数错误";
            return;
        }
        $id = $input->getEscaped('id');
        if( !$id ){
            $this->view->error = "参数错误";
            return;
        }
        $table = new Notices();
        $this->view->row = $table->getNoticeById( $id );

    }
}