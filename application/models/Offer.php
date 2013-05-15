<?php

/**
 * Model for an offer to buy or sell a commodity along with contact information for the offeror 
 *
 */

class Offer extends BaseEntity {
	
	private $_offertypelist = array(); 
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('offer');
		
		$this->hasColumn('contact', 'string', 255, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('quantity', 'decimal', 10, array('scale' => '2', 'unsigned' => true, 'notnull' => true, 'notblank' => true));
		$this->hasColumn('commodityid', 'integer', 11, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('locationid', 'integer', 11, array('notnull' => true, 'notblank' => true) );
		$this->hasColumn('telephone', 'string', 50);
		$this->hasColumn('offertype', 'tinyint', null, array('default' => '0')); // 0 - sell offer, 1 - buy offer 
		$this->hasColumn('notes', 'string', 500);
		$this->hasColumn('expirydate','date', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('price', 'integer', 11, array('notnull' => true, 'notblank' => true));
	}
	
	function construct() {
		// initialize parent settings
		parent::construct();
		
		// define the date fields
		$this->addDateFields(array("expirydate"));
		
		// define the custom error messages
       	$this->addCustomErrorMessages(array(
       									"contactid.notblank" => $this->translate->_("offer_contact_error"),
       									"commodityid.notblank" => $this->translate->_("offer_commodity_error"),
       									"locationid.notblank" => $this->translate->_("offer_location_error"),
       									"telephone.notblank" => $this->translate->_("offer_telephone_error"),
       									"quantity.notblank" => $this->translate->_("offer_quantity_error"),
       									"offertype.notblank" => $this->translate->_("offer_offertype_error"),
       									"expirydate.notblank" => $this->translate->_("offer_expirydate_error"),
       									"price.notblank" => $this->translate->_("offer_price_error")
       	       						)); 
       	 // populate the different internal lisst
       	 // offer type values
       	 $otlist = new LookupType(); 
       	 $otlist->setName("OFFER_TYPE"); 
       	 $this->_offertypelist = $otlist->getOptionValuesAndDescription(); 
		
		
	}
	
	function setUp() {
		parent::setUp();
		// define the relationships
		$this->hasOne('District as location',
						 array(
								'local' => 'locationid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('Commodity as commodity',
						 array(
								'local' => 'commodityid',
								'foreign' => 'id'
							)
					); 
	}
	
	/**
	 * Return the different types of offers
	 * 
	 * @return Array 
	 *
	 */
	function getOfferTypeList() {
		return $this->_offertypelist; 
	}
	
	/**
	 * Return the different types of offers
	 * 
	 * @return Array 
	 *
	 */
	function getOfferTypeDescription() {
		return $this->_offertypelist[$this->getOfferType()]; 
	}
}

?>