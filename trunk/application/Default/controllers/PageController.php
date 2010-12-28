<?php
class PageController extends Main_CommonController
{
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
        $table = new Pages();
        $this->view->row = $table->getPageById( $id );

        $this->view->selectItemId = "ids".$id;
    }
}