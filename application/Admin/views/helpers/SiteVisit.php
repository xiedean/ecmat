<?php
/**
 *
 * @author Administrator
 * @version 
 */
require_once 'Zend/View/Interface.php';

/**
 * Bold  helper
 *
 * @uses viewHelper Moore_View_Helper
 */
class Zend_View_Helper_SiteVisit
{

	/**
	 * @var Zend_View_Interface 
	 */
	public $view;

	/**
	 *  
	 *  @param string $string
	 */
	public function siteVisit()
    {
    	$table = new Preferences();
    	$num = $table->getPreferenceByName('site_visit');
    	return $num;
    }
	

	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
	
	
}
