<?php
/**
 * 
 * Model for an districts in which the organisation operates 
 *
 */
class OrganisationDistrict extends BaseRecord {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		$this->setTableName('organisationdistrict');
		$this->hasColumn('districtid', 'integer', null, array('notnull' => true, 'notblank' => true));	
		$this->hasColumn('organisationid', 'integer', null, array('notnull' => true, 'notblank' => true));
	}
	
	public function construct() {
		parent::construct();
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"districtid.notblank" => $this->translate->_("organisation_district_districtid__blank_error"),
       									"organisationid.notblank" => $this->translate->_("organisation_district_organisationid_blank_error")." id is".$this->getOrganisationID(),
       									"organisationid.type" => $this->translate->_("organisation_district_orgid_type_error"),
       									"organisationid.type" => $this->translate->_("organisation_district_districtid_type_error")." - ".$this->getDistrictID()
       	       						));
       	       					
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Location::setUp()
	 */
	public function setUp() {
		parent::setUp(); 
		$this->hasOne('District as district',
							array('local' => 'districtid',
									'foreign' => 'id'
							)
						);
		$this->hasOne('Organisation as organisation',
							array('local' => 'organisationid',
									'foreign' => 'id'
							)
						);
	}
}

?>