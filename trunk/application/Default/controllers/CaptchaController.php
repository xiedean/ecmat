<?php
class CaptchaController extends Moore_Controller_Action 
{
	function getImage()
	{
		Zend_Layout::startMvc(array('layoutPath'=>'./application/Default/views/layouts'));
		//   captcha
		$codeSession = new Zend_Session_Namespace('code');
		$captcha = new Zend_Captcha_Image( array(
		    'font'=>'font/arial.ttf',
		    'fontsize' => 16, 
            'imgdir' => 'images/captcha', 
            'session' => $codeSession, 
            'width' => 60, 
            'height' => 25, 
            'wordlen' => 4, 
            'dotNoiseLevel' => 5, 
            'lineNoiseLevel' => 0, 
            'gcFreq' => 10,  //How frequently to execute garbage collection
            'expiration' => 10,    //How long to keep generated images
		));

		$captcha->setGcFreq(3); //delete the code img
		$captcha->generate(); //create
		$this->view->ImgDir = $captcha->getImgDir();
		$this->view->captchaId = $captcha->getId(); //get the img'sname
		$codeSession->code=$captcha->getWord();
        $_SESSION['cc'] = $captcha->getWord();
	    $file = $this->view->baseUrl().$captcha->getImgUrl().$captcha->getId().$captcha->getSuffix();
	    $registry = Zend_Registry::getInstance();
	    $domain = $registry->get('site')->host;
	    $image = imagecreatefrompng($domain.$file);
	    imagepng($image);
	    imagedestroy($image);
	}
	
	public function indexAction()
	{
	    $this->view->addBasePath('./application/views/');
		$this->view->image = $this->getImage();
		echo $this->view->image;
	}
	
}