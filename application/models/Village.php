<?php

/**
 * Model for a village within a parish  
 *
 */

class Village extends Location {
	/*
	 * Return custom query string for existing locations
	 */
	function getUniqueQueryString(){
		return " AND locationtype = '6' AND id <> '".$this->getID()."' AND parishid = '".$this->getParishID()."'";
	}
	/*
	 * Return custom validation error message for existing location
	 */
	function getUniqueValidationErrorMessage() {
		return sprintf($this->translate->_("village_unique_name_label"), $this->getName(), $this->getParish()->getName());
	}
}

?>