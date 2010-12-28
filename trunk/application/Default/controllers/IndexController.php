<?php
class IndexController extends Main_CommonController
{
	/**
	 * init function will do first in all Action of this Controller
	 *
	 */
	public function preDispatch()
    {
        $this->view->selectItemId = "id1";  // seletct tab item
	}

	/**
	 * site index page
	 *
	 */
	function indexAction()
	{
	    if(in_array($_SERVER['SERVER_NAME'],array("echyang.com","www.echyang.com")) ){
            $this->view->site = 2;
            $this->_forward('index','yang');
            return;
	    }
	    $siteSession = new Zend_Session_Namespace('site');
        $this->view->site = $siteSession->site = 1;

	    //get articles
	    $table = new Articles();
	    $this->view->articles2 = $table->getTop2ArticleByClass(2);   //class_id 2 meams "焦点资讯"
	    $classes = new Classes();
	    $subClasses = $classes->getSubClasses( 3);                   //class_id 2 meams "行业资讯"
	    $arr = array();
        if ($subClasses) {
            foreach ($subClasses as $subc) {
                $rows = $table->getArticlesByClass($subc['class_id'], 1);
                $arr[$subc['class_id']] = $rows[0];
            }
        }
	    $this->view->classArray = $arr;

	    $this->view->imageNews = $table->getTopImageNews();         //get image news
	    $registry = Zend_Registry::getInstance();
	    $this->view->imagePath = $registry->get('newsImagePath');
	    
	    //get "热点议题1"  class_id 42
	    //$this->view->articleHot1 = $table->getArticlesByClass(42,1);
	    //get "热点议题2"  class_id 42
	    //$this->view->articleHot2 = $table->getArticlesByClass(43,1);


	}


	public function signupAction()
	{
	    $this->view->form = $form = new SignupForm();
	    $this->view->errorId = "username"; //js focus id
	    $this->view->headerName = "快速註冊用戶";

	    if( !$this->_request->isPost() ){
	        return;
	    }
	    if( !$form->isValid($this->_request->getPost()) ){
	        $errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不對";
				$this->view->errorId = $key;
			}
	        return;
	    }
	    if($form->getValue('password') != $form->getValue('password2')){
	        $this->view->notice = "註冊失敗，兩次密碼不相同";
	        return;
	    }
	    $table = new Users();
	    if($this->view->site == 2){
	        $table = new UsersYang();
	    }
	    $username_exist = $table->checkUserExist($form->getValue('username'));
	    if(count($username_exist)){
	        $this->view->notice = "註冊失敗，用戶名已經存在，請更換用戶名";
	        return;
	    }
	    $email_exist = $table->checkUserExist(null,$form->getValue('user_email'));
	    if(count($email_exist)){
	        $this->view->notice = "註冊失敗，郵箱已經被使用，請更換郵箱，如果您忘記用戶名或密碼，請點擊”忘記密碼？“來獲取您的用戶名和密碼";
	        return;
	    }
	    $data = parent::formatData( $form->getValues() );
	    $data += array(
	        'activation' => md5(microtime(true)),
	        'status' => 0,
	        'role' => 'user'
	    );

        $result = $table->insert($data);
        if ($result) {
            $registry = Zend_Registry::getInstance();
            $siteName = $registry->get('siteName');
            if($this->view->site == 2) {
                $siteName = $registry->get('siteNameYang');
            }
            $title = $siteName."註冊確認郵件";
            $body = "<p>
{$form->getValue('username')}，您好！</p>
<p>
感謝您在{$siteName}註冊，您的帳號已經成功創建，但在使用之前必須先激活您的帳號。
要激活帳號請直接點擊一下鏈接，或把它複製到瀏覽器中打開：
<a href=\"http://{$_SERVER['SERVER_NAME']}{$this->view->baseUrl()}/index/activate/activation/{$data['activation']}\">
http://{$_SERVER['SERVER_NAME']}{$this->view->baseUrl()}/index/activate/activation/{$data['activation']}</a>
</p>
<p>
帳號激活後您可以用以下會員名和密碼登陸網站“http://{$_SERVER['SERVER_NAME']}{$this->view->baseUrl()}”：
</p>
會員名：{$form->getValue('username')}<br>
密碼：{$form->getValue('password')}
            ";
            $mail = new Moore_Mail();
            $mailSent = $mail->send($form->getValue('user_email'),$title, $body);
            if(!$mailSent){
                $this->view->notice2 = "郵件發送失敗";
            }

            $noticeSession = new Zend_Session_Namespace('notice');
            $noticeSession->notice = "您的帳號已經創建，一封含有激活鏈接的郵件已經發送到您的郵箱。請注意：您只有通過點擊該激活郵件中的激活鏈接激活了您的帳號後，才能登陸網站。";
            $this->_redirect("/account/login");
            return;
        }
    }

    public function forgotAction()
    {
        $this->view->form = $form = new ForgotForm();
        $this->view->errorId = "email"; //js focus id
        $this->view->headerName = "忘記密碼？";

        if( !$this->_request->isPost() ){
            return;
        }
        if( !$form->isValid( $this->_request->getPost() ) ){
            $errors = $form->getMessages();
			foreach($errors as $key=>$err){
				$this->view->notice = $form->getElement($key)->getLabel()." 格式不對";
				$this->view->errorId = $key;
			}
	        return;
        }
        $table = new Users();
        if($this->view->site == 2){
	        $table = new UsersYang();
	    }
        $row = $table->fetchRow( "user_email = '{$form->getValue('email')}'");
        if( !$row ){
            $this->view->notice = "您的E-mail 還未註冊";
            return;
        }

        $newPs = rand(100000,999999);
        $row->password = md5($newPs);
        $id = $row->save();
        $row = $row->toArray();
        $registry = Zend_Registry::getInstance();
        $siteName = $registry->get('siteName');
        if($this->view->site == 2) {
            $siteName = $registry->get('siteNameYang');
        }
        $title = $siteName."獲取密碼郵件";
        $body = "<p>
{$row['username']}，您好！</p>
<p>
以下是您在{$siteName}的賬戶信息：
</p>
<p>
您可以用以下會員名和密碼登陸網站“http://{$_SERVER['SERVER_NAME']}{$this->view->baseUrl()}”：
</p>
會員名：{$row['username']}<br>
密碼：{$newPs}
<p>
登錄後請重新更改您的密碼。還有什麼問題請聯繫管理員：{$registry->get('siteEmail')}
</p>            ";
        $mail = new Moore_Mail();
        if( !$id ){
            $this->view->notice = "重設密碼失敗";
            return;
        }
        $mailSent = $mail->send($form->getValue('email'), $title, $body);
        if (! $mailSent) {
            $this->view->notice2 = "郵件發送失敗";
        }
        $noticeSession = new Zend_Session_Namespace('notice');
        $noticeSession->notice = "您的帳號已經更改了新的密碼並發往您的郵箱，請根據您收到的信息登陸網站。";
        $this->_redirect("/account/login");
        return;


    }

    public function activateAction()
    {
        // filter input
		$filters = array(
		    	'*'  => 'StringTrim',
	    		'*'  => 'HtmlEntities'
		);
		$validators = array(
	    		'activation' => 'Alnum'
		);
		$input = new Zend_Filter_Input($filters, $validators, $this->_request->getParams());
		if( !$input->isValid()){
			$this->view->error = "參數錯誤";
			return;
		}
		$activation = $input->getEscaped('activation');
		$table = new Users();
        if($this->view->site == 2){
	        $table = new UsersYang();
	    }
		$id = $table->updateActivation( $activation );
		$noticeSession = new Zend_Session_Namespace('notice');
		if($id) {
		    $noticeSession->notice = "帳號激活成功！您現在可以使用該帳號登錄我們的網站了";
		}
		else {
		    $noticeSession->notice = "您的鏈接錯誤或該帳號已經激活";
		}
		$this->_redirect('/account/login');
		return;
    }
    
}