<?php

require_once ('Zend/View/Helper/Url.php');

/**
 * Helper to generate a link to edit an entity.
 * 
 * The page is the default page of the current module, and controller, with an action index and additional parameters passed
 * via the options array 
 *
 */
class Zend_View_Helper_ViewUrl extends Zend_View_Helper_Url {

	function viewUrl($options) {
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if (!isArrayKeyAnEmptyString("module", $options)) {
			$options["module"] = $request->getModuleName(); 
		} 
		if (!isArrayKeyAnEmptyString("controller", $options)) {
			$options["controller"] = $request->getControllerName(); 
		} 
		$options["action"] = ACTION_VIEW; 
		return $this->url($options); 
	}
}

?>