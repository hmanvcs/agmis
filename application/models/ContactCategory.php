<?php

/**
 * The relationship between a Contact and a Business Category
 */
class ContactCategory extends BaseRecord  {
	public function setTableDefinition() {
		parent::setTableDefinition();
		$this->setTableName('contactcategory');
		$this->hasColumn('id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('contactid', 'integer', 11);
		$this->hasColumn('categoryid', 'integer', 11, array("notblank" => true));
	}
	
	public function setUp() {
		parent::setUp(); 
		$this->hasOne('Contact as contact',
							array('local' => 'contactid',
									'foreign' => 'id'
							)
						);	
		$this->hasOne('BusinessDirectoryCategory as businessdirectorycategory',
							array('local' => 'categoryid',
									'foreign' => 'id'
							)
						);	
	}
	
   /**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"categoryid.notblank" => $this->translate->_("contact_category_error"),
       	       						));
	}
}