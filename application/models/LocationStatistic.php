<?php

/**
 * Model for statistics on locations (districts, counties, subcounties, parishes) 
 *
 */

class LocationStatistic extends BaseEntity {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this-> setTableName('locationstatistic');
		
		$this->hasColumn('locationid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('startdate', 'date', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('enddate', 'date', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('statistic', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('value', 'decimal');
	}
	/**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		
		// define the date fields
		$this->addDateFields(array("startdate", "enddate"));
		
	}
}

?>