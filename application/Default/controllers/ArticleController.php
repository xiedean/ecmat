<?php
class ArticleController extends Main_CommonController
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
        $table = new Articles();
        $this->view->row = $table->getArticleById( $id );
        $this->view->content = $this->view->row['content'];
        $this->view->page = $page = $input->getEscaped('page');

        //update clicks
        $clickSession = new Zend_Session_Namespace('click');
        if(!(isset($clickSession->id) && $clickSession->id['id'] == $id)) {
            $clickSession->id = array('id'=>$id,'time'=>time());
            $table->updateClick($id);
        }
        $s = $clickSession->id;
        if($s['id'] == $id && time()-$s['time'] > 600) {
            $table->updateClick($id);
        }
        $articlePages = new ArticlePages();
        $this->view->pages = $articlePages->getArticlePageById($id);
        if($page > 1) {
            $row = $articlePages->getPage($id, $page);
            $this->view->content = $row['page_content'];
        }

    }
}