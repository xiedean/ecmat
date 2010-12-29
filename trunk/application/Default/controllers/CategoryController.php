<?php
class CategoryController extends Main_CommonController
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


        $this->view->selectItemId = "id".$id;

        $where = "a.status = '1' and a.class_id = '$id'";
        if($id == 2) {  // 焦点资讯
            $where = "a.status = '1' and (a.class_id = '$id' or a.focus = '1')";
        }
        if($id == 11) {  // 历年活动
            $where = "a.status = '1' and (a.class_id = '$id' or a.is_activity = '1')";
        }
        $classes = new Classes();
        $subClasses = $classes->getSubClasses( $id);
        if($subClasses){
            $where = "a.status = '1' and ( a.class_id = '$id' ";
            foreach($subClasses as $sc){
                $where .= " or a.class_id = '{$sc['class_id']}' ";
            }
            $where .= ")";
            $this->view->subClass = true;
        }
		$group = $input->getEscaped('group');
		$isGroup = false;
		if($group) {
			$groupTable = new ArticleGroups();
			$this->view->group = $row = $groupTable->load($group);
			if($row) {
				$isGroup = true;
				$where .= " and a.article_group_id = '$group'";
			}
		}
		$currentPageNumber = $input->getEscaped('page')>0 ?$input->getEscaped('page'):1;
		$order_spec = array("article_id DESC");

		$registry = Zend_Registry::getInstance();
		$dbAdapter = $registry->get('dbAdapter');
		$pageSize = $registry->get('adminPageSize');
		$pageRange = $registry->get('adminPageRange');

		$table = new Articles();
		$query = $table->getAllQuery( $this->view->site,$where,$order_spec,null, $isGroup);

		$paginator = Zend_Paginator::factory($query);
		$paginator->setCurrentPageNumber($currentPageNumber)
				  ->setItemCountPerPage($pageSize)
        		  ->setPageRange($pageRange);

		$this->view->paginator = $paginator;
		$this->view->count = $paginator->getCurrentItemCount();
		$this->view->className = $classes->getClassName($id);
    }
}