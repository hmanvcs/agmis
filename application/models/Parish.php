<?php

/**
 * Model for a parish within a subcounty  
 *
 */

class Parish extends Location {
	/*
	 * Return custom query string for existing locations
	 */
	function getUniqueQueryString(){
		return " AND locationtype = '5' AND id <> '".$this->getID()."' AND subcountyid = '".$this->getSubcountyID()."'";
	}
	/*
	 * Return custom validation error message for existing location
	 */
	function getUniqueValidationErrorMessage() {
		return sprintf($this->translate->_("parish_unique_name_label"), $this->getName(), $this->getSubcounty()->getName());
	}
}

?>