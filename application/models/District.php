<?php

/**
 * Model for a district within a region
 *
 */

class District extends Location {
	/*
	 * Return custom query string for existing locations
	 */
	function getUniqueQueryString(){
		return " AND locationtype = '2' AND id <> '".$this->getID()."' ";
	}
	/*
	 * Return custom validation error message for existing location
	 */
	function getUniqueValidationErrorMessage() {
		return sprintf($this->translate->_("district_unique_name_label"), $this->getName());
	}
}

?>