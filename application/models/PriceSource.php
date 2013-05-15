<?php

class PriceSource extends BaseEntity {
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('pricesource');
		$this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank' => true, "unique" => true));
		$this->hasColumn('contactperson', 'string', 255);
		$this->hasColumn('phone', 'string', 15, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('email', 'string', 255);
		$this->hasColumn('address', 'string', 255, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('notes', 'string', 1000);
		$this->hasColumn('locationid', 'integer', array('notnull' => true, 'notblank' => true));
		$this->hasColumn('applicationtype', 'integer', null, array('notnull' => true, 'notblank' => true));
	}
	/**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"name.notblank" => $this->translate->_("pricesource_name_error"),								
       									"name.unique" => $this->translate->_("pricesource_name_unique_error"),
       									"phone.notblank" => $this->translate->_("pricesource_phone_error"),
       									"address.notblank" => $this->translate->_("pricesource_address_error"),
       									"locationid.notblank" => $this->translate->_("pricesource_location_error")
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
		// the different price categories for the commodity
		$this->hasMany('PriceSourceCategory as pricecategories',
							array('local' => 'id',
									'foreign' => 'pricesourceid'
							)
						);  
	}
	/*
	 * 
	 */
	function processPost($formvalues){
		// check if the locationid is not specified and remove it from POST
		if (isArrayKeyAnEmptyString('locationid', $formvalues)) {
			$formvalues['locationid'] = NULL;
		}
		
		if (isArrayKeyAnEmptyString('pricecategories', $formvalues)) {
			unset($formvalues['pricecategories']); 
		}
		
		// move the data from $formvalues['pricesource_pricecategoryid'] into $formvalues['pricecategories'] array
		if (array_key_exists('pricesource_pricecategoryid', $formvalues)) {
			$pricecategoryids = $formvalues['pricesource_pricecategoryid']; 
			$pricecategories = array(); 
			foreach ($pricecategoryids as $id) {
				$pricecategories[]['pricecategoryid'] = $id; 
			}
			$formvalues['pricecategories'] = $pricecategories; 
			// remove the contact_categoryid array, it will be ignored, but to be on the safe side
			unset($formvalues['pricesource_pricecategoryid']); 
		}
		
		foreach($formvalues['pricecategories'] as $key => $value){
			if(isEmptyString($value['pricecategoryid'])){
				unset($formvalues['pricecategories'][$key]); 
			}
		}
		parent::processPost($formvalues);
	}
	/**
     * Return an array containing the IDs of the price categories that the pricesource belongs to
     *
     * @return Array of the IDs of the price categories that the pricesource belongs to
     */
    function getPriceCategoryIDs() {
        $ids = array();
        $pricecategories = $this->getPriceCategories();
        //debugMessage($pricecategories->toArray());
        foreach($pricecategories as $thepricecategories) {
            $ids[] = $thepricecategories->getPriceCategoryID();
        }
        return $ids;
    }
	/**
     * Display a list of price categories that the price source belongs
     *
     * @return String HTML list of the price categories that the price source belongs to
     */
    function displayCategories() {
        $pricecategories = $this->getPriceCategories();
        $str = "";
        if ($pricecategories->count() == 0) {
            return $str;
        }
        $str .= '<ul class="list">';
        foreach($pricecategories as $thepricecategories) {
            $str .= "<li>".$thepricecategories->getPriceCategory()->getName()."</li>"; 
        }
        $str .= "</ul>";
        return $str; 
    }
	/**
     * The full text of the application type
     * 
     * @return String 
     */
    function getApplicationTypeDescription() {
    	return LookupType::getLookupValueDescription("APPLICATION_TYPE", $this->getApplicationType()); 
    }
	# determine pricesource id from searchable name
    function findByName($name) {
    	$str_len = strlen($name);
    	trim($name);
    	$name = str_replace('market', '', strtolower($name));
		$conn = Doctrine_Manager::connection();
		// query for check if location exists
		$unique_query = "SELECT p.id FROM pricesource p inner join pricesourcecategory ps on (p.id = ps.pricesourceid) where (ps.pricecategoryid = 2 AND p.name LIKE '%".$name."%') ";
		$result = $conn->fetchOne($unique_query);
		// debugMessage($unique_query);
		// debugMessage($result);
		return $result; 
	}
}
?>
