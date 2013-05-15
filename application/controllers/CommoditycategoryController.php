<?php

class CommoditycategoryController extends SecureController {
	
	/**
	 * @see SecureController::getResourceForACL()
	 *
	 * @return String
	 */
	function getResourceForACL() {
		return "Commodity Category"; 
	}

}