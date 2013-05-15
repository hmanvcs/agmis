<?php

class PricesourceController extends SecureController  {
	/**
	 * @see SecureController::getResourceForACL()
	 *
	 * @return String
	 */
	function getResourceForACL() {
		return "Price Source"; 
	}
	
	function getActionforACL(){
		return ACTION_VIEW;
	}
	
	function pdfAction(){
		$this->_helper->layout->disableLayout();
		
		// debugMessage($this->_getAllParams());
	}
}