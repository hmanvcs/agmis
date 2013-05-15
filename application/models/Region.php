<?php

/**
 * Model for a region, the highest level type of location 
 *
 */

class Region extends Location {
	
	/*
	 * Return custom query string for existing locations
	 */
	function getUniqueQueryString(){
		return " AND locationtype = '1' AND id <> '".$this->getID()."' ";
	}
	/*
	 * Return custom validation error message for existing location
	 */
	function getUniqueValidationErrorMessage() {
		return sprintf($this->translate->_("region_unique_name_label"), $this->getName());
	}
	
}

?>