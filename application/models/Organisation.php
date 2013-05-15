<?php

/**
 * Model for Organisations in the different districts
 *
 */

class Organisation extends BaseEntity {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		$this->setTableName('organisation');
		$this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('abbreviation', 'string', 25);
		$this->hasColumn('address', 'string', 500);
		$this->hasColumn('phone', 'sting', 50);
		$this->hasColumn('logo','string', 55);
		$this->hasColumn('fax','string', 50);
		$this->hasColumn('email','string', 255);
		$this->hasColumn('website','string', 255);
		$this->hasColumn('subscriptionexpirydate','string', 50);
		$this->hasColumn('parentid', 'integer', null); 
	
		// unique constraint on name
		$this->unique(array("name")); 
	}
	
	/**
	 * Contructor method for custom functionality - add the required fields
	 */
	public function construct() {
		parent::construct();
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"name.notblank" => $this->translate->_("organisation_name_error"),
								       	"name.null" => $this->translate->_("organisation_name_error"),
       									"name.unique" => $this->translate->_("organisation_name_unique_error")
       	       						));
       	       					
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::setUp()
	 */
	public function setUp() {
		parent::setUp(); 
		$this->hasMany('OrganisationDistrict as organisationdistricts',
							array('local' => 'id',
									'foreign' => 'organisationid'
							)
						);
		$this->hasOne('Organisation as parent',
						 array(
								'local' => 'parentid',
								'foreign' => 'id'
							)
					); 
		
	}
	
	/**
     * Display a list of districts that the organization operates in  
     *
     * @return String HTML list of the districts that the organization operates in 
     */
    function displayDistrictsOfOperation() {
        $orgdistricts = $this->getOrganisationDistricts();
        if ($orgdistricts->count() == 0) {
            return "";
        }
        $data_array = array(); 
        foreach($orgdistricts as $theorgdistrict) {
            $data_array[] = $theorgdistrict->getDistrict()->getName(); 
        }
        return createHTMLListFromArray($data_array); 
    }
    /**
     *Get all district ids  
     *
     * @return array of the districts ids that the 
     */
	function getAllDistrictIDs() {
        $orgdistricts = $this->getOrganisationDistricts();
        if ($orgdistricts->count() == 0) {
            return "";
        }
        $districtid_array = array(); 
        foreach($orgdistricts as $theorgdistrict) {
            $districtid_array[] = $theorgdistrict->getDistrict()->getID(); 
        }
        return $districtid_array; 
    }
    /**
     * for each organisation saved, the details its districts are to be saved
     * in different table using the organisationdistricts relationship
     * the organisationdistricts table expects the id of the org and the district id
     * @see BaseRecord::processPost()
     */
	function processPost($formvalues) {
		//form the organidationdistricts array
		$organisationdistricts = array();
		$i = 0;
		foreach ($formvalues['districtid'] as $districtid) {
			$organisationdistricts[$i]['districtid'] = $districtid;
			if(!isEmptyString($formvalues['id'])){
				$organisationdistricts[$i]['organisationid'] = $formvalues['id']; 
			}
			$i++;
		}
		
		$formvalues['organisationdistricts']= $organisationdistricts;
		
		if(isArrayKeyAnEmptyString('parentid', $formvalues)){
			unset($formvalues['parentid']);
		}
		if(isArrayKeyAnEmptyString('logo', $formvalues)){
			unset($formvalues['logo']);
		}
		parent::processPost($formvalues);
	}

}

?>