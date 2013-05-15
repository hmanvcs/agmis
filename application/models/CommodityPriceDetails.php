<?php

/**
 * The relationship between Commodity Price Submission and Details
 */
class CommodityPriceDetails extends BaseRecord  {
	public function setTableDefinition() {
		parent::setTableDefinition();
		$this->setTableName('commoditypricedetails');
		$this->hasColumn('id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('submissionid', 'integer', 11, array("notblank" => true));
		$this->hasColumn('pricecategoryid', 'integer', 11, array("notblank" => true));
		$this->hasColumn('commodityid', 'integer', 11);
		$this->hasColumn('retailprice', 'decimal', 11, array('default' => '0'));
		$this->hasColumn('wholesaleprice', 'decimal', 11, array('default' => '0'));
		$this->hasColumn('notes', 'string', 1000);
		$this->hasColumn('datecollected','date', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('sourceid', 'integer', 11, array('notnull' => true, 'notblank' => true));
		
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
       									"pricecategoryid.notblank" => $this->translate->_("commoditypricedetails_pricecategory_error")
       	       						));
	}
	/*
	 * 
	 */
	function processPost($formvalues){
		// Custom processing for integer and enum fields
		if(isArrayKeyAnEmptyString('retailprice', $formvalues)){
			unset($formvalues['retailprice']); 
		}
		if(isArrayKeyAnEmptyString('wholesaleprice', $formvalues)){
			unset($formvalues['wholesaleprice']); 
		}
		
		parent::processPost($formvalues);
	}
}