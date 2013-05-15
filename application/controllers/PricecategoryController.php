<?php

class PricecategoryController extends SecureController {
	
	/**
	 * @see SecureController::getResourceForACL()
	 *
	 * @return String
	 */
	function getResourceForACL() {
		return "Price Category"; 
	}

}