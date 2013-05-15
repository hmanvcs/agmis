<?php

/**
 * The relationship between a Price Source and its Price Categories
 */
class PriceSourceCategory extends BaseRecord  {
	public function setTableDefinition() {
		parent::setTableDefinition();
		$this->setTableName('pricesourcecategory');
		$this->hasColumn('id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('pricesourceid', 'integer', 11);
		$this->hasColumn('pricecategoryid', 'integer', 11, array("notblank" => true));
	}
	
	public function setUp() {
		parent::setUp(); 
		$this->hasOne('PriceSource as pricesource',
							array('local' => 'pricesourceid',
									'foreign' => 'id'
							)
						);	
		$this->hasOne('PriceCategory as pricecategory',
							array('local' => 'pricecategoryid',
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
       									"pricecategoryid.notblank" => $this->translate->_("pricesource_pricecategoryid_error"),
       	       						));
	}
}