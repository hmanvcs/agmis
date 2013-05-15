<?php

/**
 * Model for a subcounty within a county  
 *
 */

class Subcounty extends Location {
	/*
	 * Return custom query string for existing locations
	 */
	function getUniqueQueryString(){
		return " AND locationtype = '4' AND id <> '".$this->getID()."' AND countyid = '".$this->getCountyID()."'";
	}
	/*
	 * Return custom validation error message for existing location
	 */
	function getUniqueValidationErrorMessage() {
		return sprintf($this->translate->_("subcounty_unique_name_error"), $this->getName(), $this->getCounty()->getName());
	}
}

?>