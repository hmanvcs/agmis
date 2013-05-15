<?php

/**
 * The relationship between a Commodity and a Price Category
 */
class CommodityPriceCategory extends BaseRecord  {
	public function setTableDefinition() {
		parent::setTableDefinition();
		$this->setTableName('commoditypricecategory');
		$this->hasColumn('id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('commodityid', 'integer', 11);
		$this->hasColumn('pricecategoryid', 'integer', 11, array("notblank" => true));
	}
	
	public function setUp() {
		parent::setUp(); 
		$this->hasOne('Commodity as commodity',
							array('local' => 'commodityid',
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
       									"pricecategoryid.notblank" => $this->translate->_("commodity_pricecategory_error"),
       	       						));
	}
}