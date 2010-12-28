<?php
class Myemail
{
	protected $email;
	protected $content;
	protected $subject;
	protected $sitename;
	protected $sign;
	
	
	/**
	 * format class
	 *
	 * @param string $email
	 */
	public function __construct($email)
	{
		$this->email = $email;
		$site = Zend_Registry::get('site');
		$this->sitename = $site->host;
		if(substr($this->sitename,-1) == "/"){
		    $this->sitename = substr($this->sitename,0,strlen($this->sitename)-1);
		}
		$this->sign = "<br> Vedu";
	}
	
	/**
	 * get baseUrl of the site.
	 *
	 * @return string
	 */
	function baseurl()
	{
		$fc = Zend_Controller_Front::getInstance();
		return $fc->getBaseUrl();
	}
	
	/**
	 * send email 
	 *
	 * @return bool
	 */
	public  function sendemail()
	{
		$mail = new Zend_Mail('utf-8');	
		$mail->setFrom('info@briefmeetings.com', "BriefMeetings");
		$mail->setSubject($this->subject);
		$mail->setBodyHtml($this->content);
		$mail->addTo($this->email);
		return $mail->send();
	}
	
	/**
	 * when sign up, send confirm email to the new user email account.
	 *
	 * @param string $name
	 * @param string $code
	 * @param string $controller
	 * @param string $action
	 */
	public  function sendconfirm($name,$code,$url)
	{
		$baseurl = $this->baseurl();
		$this->content = "尊敬 $name:<br><br>
<p>感谢您在我们网站上注册，请点击下面的链接激活您的帐户：<br>
<a href='$this->sitename$baseurl/$url/code/$code/email/$this->email'>$this->sitename$baseurl/$url/code/$code/email/$this->email</a><br>
(如果链接无效，请尝试复制该地址并粘贴到浏览器的地址栏)<br><br></p>
谢谢.";
        $this->content .= $this->sign;
        $this->subject = "BriefMeetings Registration Confirmation";
        return $this->sendemail();
	}
	
	/**
	 * guest send email to site in contact page.
	 *
	 * @param string $name
	 * @param string $subject
	 * @param string $content
	 * @param string $from
	 */
	public function sendcontact($name,$subject,$content,$from)
	{
		$this->content = $content;
		$mail = new Zend_Mail('utf-8');
		$mail->setFrom($from,$name);
		$mail->setSubject($subject);
		$mail->setBodyHtml($this->content);
		$mail->addTo($this->email);
		return $mail->send();
	}
	
	/**
	 * sent reset password email
	 *
	 * @param string $name
	 * @param string $controller
	 * @param string $action
	 * @param string $cc
	 * @param string $tt
	 */
	public function sendreset($name,$url,$cc,$tt)
	{
		$baseurl = $this->baseurl();
		$email = urlencode($this->email);
		$this->content = "Hello $name:<br><br>
		<p>You recently applied for a new password.To reset your password, visit the following links:<br>
		<a href='$this->sitename$baseurl/$url/email/$email/cc/$cc/tt/$tt'>$this->sitename$baseurl/$url/email/$email/cc/$cc/tt/$tt</a><br>
		(If clicking on the link doesn't work, try copying and pasting it into your browser.)<br><br></p>
		Thank you.<br>
		The BriefMeetings team.";
		$this->subject = "BriefMeetings Password reset confirmation";
		return $this->sendemail();
	}
	
	/**
	 * when guest leave contact info to watch video, send video link to the left email address.
	 *
	 * @param string $name
	 * @param string $controller
	 * @param string $action
	 * @param int $video_id
	 * @param string $video_name
	 * @param string $secret
	 * @param string $secretContact
	 */
	public function sendContectConf($name,$url,$video_id,$video_name,$secret,$secretContact) 
	{
		$baseurl = $this->baseurl();
		$this->content = " Hello $name: <br><br>
		<p>Welcome to BriefMeetings and click the link below to watch \"<b>$video_name</b>\" <br>
		<a href='$this->sitename$baseurl/$url/secretcontact/$secretContact/video_id/$video_id/secret/$secret/email/$this->email'>$this->sitename$baseurl/$url/secretcontect/$secretContact/video_id/$video_id/secret/$secret/email/$this->email </a><br>
		(If clicking on the link doesn't work, try copying and pasting it into your browser.)<br><br></p>
		Thank you.<br>
		The BriefMeetings team.";
		$this->subject = "BriefMeetings confirmation message";
		return $this->sendemail();
	}
	
	/**
	 * after viewer pay, send payment info to his email address.
	 *
	 * @param string $name
	 * @param string $content
	 */
	public function sendpayment($name,$content)
	{
		$this->subject = "Payment notification on BriefMeetings";
		$this->content = " Hello $name:<br><br>";
		$this->content .= "<p><h3>Thank you for payment!</h3></p><br>The following data was about the payment:<br>";
		$this->content .= $content."<p>Thank you.<br> The BriefMeetings team.</p>";
		
		return $this->sendemail();
	}
	
}
