<?php

class Contact extends BaseEntity {
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('contact');
		$this->hasColumn('salutation', 'string', 10);
		$this->hasColumn('gender', 'enum', null, array('values' => array(0 => 'M', 1 => 'F')));
		$this->hasColumn('firstname', 'string', 100);
		$this->hasColumn('lastname', 'string', 100);
		$this->hasColumn('othernames', 'string', 100);
		$this->hasColumn('orgname', 'string', 255);
		$this->hasColumn('contacttype', 'tinyint', array('notnull' => true, 'notblank' => true));
		$this->hasColumn('contactperson', 'string', 255);
		$this->hasColumn('phone', 'string', 25, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('phone2', 'string', 25);
		$this->hasColumn('email', 'string', 255);
		$this->hasColumn('address', 'string', 255, array('notnull' => true, 'notblank' => true));

		$this->hasColumn('locationid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('subcountyid', 'integer', null);
		$this->hasColumn('idorpassportno', 'string', 255);
		$this->hasColumn('driverlicenceno', 'string', 255);
		$this->hasColumn('licenceno', 'string', 255);
		$this->hasColumn('dateofregistration', 'date', null);
		$this->hasColumn('numberofmale', 'integer', null);
		$this->hasColumn('numberoffemale', 'integer', null);
		$this->hasColumn('businessdescription', 'clob', array('notnull' => true, 'notblank' => true));
		$this->hasColumn('goodsorservicesoffered', 'clob', array('notnull' => true, 'notblank' => true));
		$this->hasColumn('numberofoutlets', 'integer', null);
		$this->hasColumn('wishtoadvertise', 'integer', null, array('default' => 0));
		$this->hasColumn('goodsorservicetoadvertise', 'clob');
		$this->hasColumn('vatnumber', 'string', 255);
		$this->hasColumn('tinnumber', 'string', 255);
		
		
		$this->setSubclasses(array(
				'Person' => array('contacttype' => 1),
				'Company' => array('contacttype' => 2)
			)
		);
		
		// unique constraint
		// $this->unique(array("firstname", "lastname", "othernames", "orgname"));
		
	}
	/**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		
		// define the date fields
		$this->addDateFields(array("dateofregistration"));
		// set the default contract type
		if(isEmptyString($this->getContactType())){
			$this->setContactType($this->config->contact->defaultcontacttype);
		}
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"contacttype.notblank" => $this->translate->_("contact_contactytype_label"),								
       									"phone.notblank" => $this->translate->_("contact_phone_error"),
       									"address.notblank" => $this->translate->_("contact_address_error")
       	       						));
	}
	
	public function setUp() {
		parent::setUp(); 
		
		$this->hasOne('District as location',
						 array(
								'local' => 'locationid',
								'foreign' => 'id'
							)
					); 
		$this->hasMany('ContactCategory as contactcategories',
							array('local' => 'id',
									'foreign' => 'contactid'
							)
						);
	}
	/*
	 * 
	 */
	function validate() {
		parent::validate();
		
		$conn = Doctrine_Manager::connection();
		
		# Validate that the contact firstname,lastname,othernames are unique for person contacttype
		if($this->getContactType() == "1"){
			# Check that the firstname been specified
			if(isEmptyString($this->getFirstName())){
				$this->getErrorStack()->add("firstname.notblank", $this->translate->_("useraccount_firstname_error"));
			}
			# Check that the lastname been specified
			if(isEmptyString($this->getLastName())){
				$this->getErrorStack()->add("lastname.notblank", $this->translate->_("useraccount_lastname_error"));
			}
			# Check that the salutation been specified
			if(isEmptyString($this->getSalutation())){
				$this->getErrorStack()->add("salutation.notblank", $this->translate->_("contact_salutation_error"));
			}
			# Check that the gender been specified
			if(isEmptyString($this->getGender())){
				$this->getErrorStack()->add("gender.notblank", $this->translate->_("contact_gender_error"));
			}

			# Validate that the person firstname, lastname and othernames are unique for person contacttype 
			$person_query = "SELECT id FROM contact WHERE firstname = '".$this->getFirstName()."' AND lastname = '".$this->getLastName()."' AND othernames = '".$this->getOtherNames()."' AND id <> '".$this->getID()."'";		
			$person_result = $conn->fetchOne($person_query);
			if(!isEmptyString($person_result)){ 
				$this->getErrorStack()->add("contact.unique", sprintf($this->translate->_("contact_person_unique_error"), $this->getFirstName()." ".$this->getLastName()));
			}
			
		}
		
		# Custom validation for Company contact type 
		if($this->getContactType() == "2"){
			# Check that the company name has been specified
			if(isEmptyString($this->getOrgName())){
				$this->getErrorStack()->add("orgname.notblank", $this->translate->_("contact_orgname_error"));
			}
			
			# Validate that the company name is unique for company contacttype
			$company_query = "SELECT id FROM contact WHERE orgname = '".$this->getOrgName()."' AND contacttype = '2' AND id <> '".$this->getID()."'";
			$company_result = $conn->fetchOne($company_query);
			if(!isEmptyString($company_result)){ 
				$this->getErrorStack()->add("company.unique",  sprintf($this->translate->_("contact_person_unique_error"),$this->getOrgName()));
			}
		}
	}
	/*
	 * 
	 */
	function processPost($formvalues){
		// check if the locationid is specified
		if (isArrayKeyAnEmptyString('locationid', $formvalues)) {
			$formvalues['locationid'] = NULL;
		}
		//unset number fields that can be empty
		if (isArrayKeyAnEmptyString('contactcategories', $formvalues)) {
			unset($formvalues['contactcategories']); 
		}
		//reset all empty number fields to empty string
		if(isArrayKeyAnEmptyString('numberofmale', $formvalues)){
			unset($formvalues['numberofmale']); 
		}
		if(isArrayKeyAnEmptyString('numberoffemale', $formvalues)){
			unset($formvalues['numberoffemale']); 
		}
	    if(isArrayKeyAnEmptyString('numberofoutlets', $formvalues)){
			unset($formvalues['numberofoutlets']); 
		}
		if(isArrayKeyAnEmptyString('wishtoadvertise', $formvalues)){
			unset($formvalues['wishtoadvertise']); 
		}
		
		// move the data from $formvalues['contact_categoryid'] into $formvalues['contactcategories'] array
		if (array_key_exists('contact_categoryid', $formvalues)) {
			$categoryids = $formvalues['contact_categoryid']; 
			$contactcategories = array(); 
			foreach ($categoryids as $id) {
				$contactcategories[]['categoryid'] = $id; 
			}
			$formvalues['contactcategories'] = $contactcategories; 
			// remove the contact_categoryid array, it will be ignored, but to be on the safe side
			unset($formvalues['contact_categoryid']); 
		}
		
		foreach($formvalues['contactcategories'] as $key => $value){
			if(isEmptyString($value['categoryid'])){
				unset($formvalues['contactcategories'][$key]); 
			}
		}
		// debugMessage($formvalues);		
		parent::processPost($formvalues);
	}
	/**
	 * Returns the full name of the Contact. If the contact is a person, return a concatination on the firstname and the lastname. 
	 * Else if a Company, return the organization name
	 *
	 * @return String
	 */
	function getName() {
		if ($this->getContactType() == '1') {
			return $this->getSalutation(). ". ".$this->getFirstName() . " " . $this->getLastName(). " " . $this->getOtherNames();
		} else {
			return $this->getOrgName();
		}
	}
	/**
     * Return an array containing the IDs of the categories that the contacts belongs to
     *
     * @return Array of the IDs of the categories that the contacts belongs to
     */
    function getCategoryIDs() {
        $ids = array();
        $categories = $this->getContactCategories();
        //debugMessage($categories->toArray());
        foreach($categories as $thecategories) {
            $ids[] = $thecategories->getCategoryID();
        }
        return $ids;
    }
	/**
     * Display a list of categories that the contact belongs
     *
     * @return String HTML list of the categories that the contact belongs to
     */
    function displayCategories() {
        $categories = $this->getContactCategories();
        $str = "";
        if ($categories->count() == 0) {
            return $str;
        }
        $str .= '<ul class="list">';
        foreach($categories as $thecategories) {
            $str .= "<li>".$thecategories->getBusinessDirectoryCategory()->getName()."</li>"; 
        }
        $str .= "</ul>";
        return $str; 
    }
	
}
?>
