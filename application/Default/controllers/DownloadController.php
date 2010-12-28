<?php
class DownloadController extends Main_CommonController
{

    public function filesAction()
    {
        $filter = new Filter();
        $input = $filter->filterValid($this->_request->getParams());
        if( !$input ){
            $this->view->error = "參數錯誤";
            return;
        }

        $table = new Files();
        $this->view->rows = $table->getFilesBySite(2);

        $this->view->selectItemId = "idsDownload";
        $this->view->headerName = "下載專區";
        $registry = Zend_Registry::getInstance();
        $this->view->path = $registry->get('uploadPath');
    }

    public function magazineAction()
    {
        $filter = new Filter();
        $input = $filter->filterValid($this->_request->getParams());
        if( !$input ){
            $this->view->error = "參數錯誤";
            return;
        }

        $table = new Files();
        $this->view->rows = $table->getFilesBySite(1);

        $this->view->selectItemId = "idsMagazine";
        $this->view->headerName = "傳管會刊";
        $registry = Zend_Registry::getInstance();
        $this->view->path = $registry->get('uploadPath');


    }

    public function pdfAction()
    {
        $file_id = $this->_request->getParam('file',null);
        $registry = Zend_Registry::getInstance();
        $this->view->path = $registry->get('uploadPath');

        $table = new Files();
        $this->view->row = $row = $table->getFileById($file_id);

    }
}