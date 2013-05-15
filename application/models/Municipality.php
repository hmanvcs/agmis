<?php

/**
 * Model for a Municipality within a district  
 *
 */

class Municipality extends Location {
	/*
	 * Return custom query string for existing locations
	 */
	function getUniqueQueryString(){
		return " AND locationtype = '7' AND id <> '".$this->getID()."' AND districtid = '".$this->getDistrictID()."'";
	}
	/*
	 * Return custom validation error message for existing location
	 */
	function getUniqueValidationErrorMessage() {
		return sprintf($this->translate->_("municipality_unique_name_label"), $this->getName(), $this->getDistrict()->getName());
	}
}

?>