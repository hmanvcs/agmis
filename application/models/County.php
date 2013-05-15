<?php

/**
 * Model for a county within a district 
 *
 */

class County extends Location {
	/*
	 * Return custom query string for existing locations
	 */
	function getUniqueQueryString(){
		return " AND locationtype = '3' AND id <> '".$this->getID()."' AND districtid = '".$this->getDistrictID()."'";
	}
	/*
	 * Return custom validation error message for existing location
	 */
	function getUniqueValidationErrorMessage() {
		return sprintf($this->translate->_("county_unique_name_label"), $this->getName(), $this->getDistrict()->getName());
	}
}

?>