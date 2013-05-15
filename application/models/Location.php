<?php

/**
 * Model for a location
 *
 */

abstract class Location extends BaseEntity {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('location');
		$this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('description', 'string', 500);
		$this->hasColumn('locationtype', 'tinyint');
		$this->hasColumn('regionid','integer');
		$this->hasColumn('districtid','integer');
		$this->hasColumn('countyid','integer');
		$this->hasColumn('subcountyid','integer');
		$this->hasColumn('parishid','integer');

		$this->setSubclasses(array(
				'Region' => array('locationtype' => 1),
				'District' => array('locationtype' => 2),
				'County' => array('locationtype' => 3),
				'Subcounty' => array('locationtype' => 4),
				'Parish' => array('locationtype' => 5),
				'Village' => array('locationtype' => 6),
				'Municipality' => array('locationtype' => 7)
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
       									"name.notblank" => $this->translate->_("region_name_error")
       	       						));
	}
	public function setUp() {
		parent::setUp(); 
		# the relationships to the different location types
		$this->hasOne('Region as region',
						 array(
								'local' => 'regionid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('District as district',
						 array(
								'local' => 'districtid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('County as county',
						 array(
								'local' => 'countyid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('Subcounty as subcounty',
						 array(
								'local' => 'subcountyid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('Parish as parish',
						 array(
								'local' => 'parishid',
								'foreign' => 'id'
							)
					); 
		$this->hasMany('OrganisationDistrict as organisationdistricts',
							array('local' => 'id',
									'foreign' => 'organisationid'
							)
						);
			// the details of the location
		$this->hasOne('LocationDetail as locationdetail',
						 array(
								'local' => 'id',
								'foreign' => 'id'
							)
					);
		
					
	}
	/*
	 * 
	 */
	function processPost($formvalues){
		// Custom processing for integer and enum fields
		if (!isArrayKeyAnEmptyString('name', $formvalues)) {
			$formvalues['name'] = trim($formvalues['name']);
		}
		if (isArrayKeyAnEmptyString('regionid', $formvalues)) {
			$formvalues['regionid'] = NULL;
		}
		if (isArrayKeyAnEmptyString('districtid', $formvalues)) {
			$formvalues['districtid'] = NULL;
		}
		if (isArrayKeyAnEmptyString('countyid', $formvalues)) {
			$formvalues['countyid'] = NULL;
		}
		if (isArrayKeyAnEmptyString('subcountyid', $formvalues)) {
			$formvalues['subcountyid'] = NULL;
		}
		if (isArrayKeyAnEmptyString('parishid', $formvalues)) {
			$formvalues['parishid'] = NULL;
		}
		if (isArrayKeyAnEmptyString('villageid', $formvalues)) {
			$formvalues['villageid'] = NULL;
		}
		// debugMessage($formvalues);
		parent::processPost($formvalues);
	}
	/*
	 * 
	 */
	function validate() {
		// execute validation in parent
		parent::validate();
		
		# check that region is unique for locationtype = 1
		if (!$this->locationExists()) {
			$this->getErrorStack()->add("name.unique", $this->getUniqueValidationErrorMessage());
		}
	}
	/*
	 * Validate that the location name is unique depending on the location type 
	 */
	function locationExists() {
		// connection		
		$conn = Doctrine_Manager::connection();
		
		// query for check if location exists
		$unique_query = "SELECT id FROM location WHERE name = '".$this->getName()."' ".$this->getUniqueQueryString();
		$result = $conn->fetchOne($unique_query);
		//debugMessage($unique_query);
		//debugMessage($result);
		if(!isEmptyString($result)){ 
			return false;
		}
		
		return true;
	}
	
		
	abstract public function getUniqueQueryString();
	
	abstract public function getUniqueValidationErrorMessage();
	/**
	 * Factory method for the different location subclasses 
	 * 
	 *  @param Integer $type
	 *  
	 *  @return Location subclass 
	 */
	static function getInstance($type) {
		switch ($type) {
			case 1: return new Region(); 
			case 2: return new District();
			case 3: return new County(); 
			case 4: return new Subcounty(); 
			case 5: return new Parish(); 
			case 6: return new Village(); 
			case 7: return new Municipality(); 
		}
	}
	
/**
     * The current market prices for a district 
     * @param int $this->getID()
     * @return Array of lattest commodity prices 
     */
	function getMarketCurrentPrices() {
		$conn = Doctrine_Manager::connection();
		
		$all_results_query = "SELECT 
			  d.datecollected AS datecollected,
			  cd.`name`,
			  ROUND(AVG(d.retailprice), -2) AS `Retail Price`,
			  ROUND(AVG(d.wholesaleprice), -2) AS `Wholesale Price`
			FROM
			  commoditypricedetails AS d 
			  INNER JOIN commodity cd ON d.`commodityid` = cd.`id`
			  INNER JOIN 
			    (SELECT 
			      cp.sourceid,
			      MAX(cp.datecollected) AS datecollected,
			      p.name 
			    FROM
			      commoditypricedetails cp 
			      INNER JOIN commoditypricesubmission AS cs1 
			        ON (
			          cp.`submissionid` = cs1.`id` 
			          AND cs1.`status` = 'Approved'
			        ) 
			      INNER JOIN pricesource AS p 
			        ON (
			          cp.sourceid = p.id 
			          AND p.`applicationtype` = 0 AND p.locationid = '".$this->getID()."'
			        ) 
			    WHERE cp.`pricecategoryid` = 2  
			    GROUP BY cp.sourceid) AS d2 
			    ON (
			      d.`sourceid` = d2.sourceid 
			      AND d.`datecollected` = d2.datecollected
			    ) 
			WHERE d.`pricecategoryid` = 2  
			GROUP BY d.`commodityid`
			ORDER BY cd.name ";
		
		// debugMessage($all_results_query);
		return $conn->fetchAll($all_results_query);
	}
	/**
     * The current fuel prices for a district 
     * @param int $this->getID(), int $commodityid
     * @return The lattest fuel prices in a district
     * 
     * TODO: Change this method to return an array of the latest fuel prices for all commodities in the fuel price category instead of running a
     * query for each commodity 
     */
	function getCurrentFuelPrices($commodityid) {
		$conn = Doctrine_Manager::connection();
		$all_results_query = "SELECT 
				ROUND(AVG(d.retailprice), -2) AS `Retail Price`, 		
				d.datecollected as datecollected, 
				d.sourceid as sourceid, 
				d2.name as `Market`, 
				d2.locationid as `districtid`, 
				d2.districtname as `District Name` 
				FROM commoditypricedetails AS d 
				INNER JOIN ( SELECT cp.sourceid, 
								MAX(cp.datecollected) AS datecollected, 
								p.name, 
								p.locationid, 
								l.name as districtname 
								FROM commoditypricedetails cp 
								INNER JOIN commoditypricesubmission AS cs1 
								ON (cp.`submissionid` = cs1.`id` AND cs1.`status` = 'Approved') 
								INNER JOIN pricesource AS p ON (cp.sourceid = p.id AND p.`applicationtype` = 0 ) 
								INNER JOIN location AS l on (p.locationid = l.id AND l.locationtype = 2) 
								WHERE cp.`commodityid` = '".$commodityid."' AND cp.`pricecategoryid` = 4 GROUP BY cp.sourceid) AS d2 
				ON (d.`sourceid` = d2.sourceid AND d.`datecollected` = d2.datecollected) 
				WHERE d.`commodityid` = '".$commodityid."' AND d2.locationid = '".$this->getID()."' AND d.`pricecategoryid` = 4 
				GROUP BY d2.locationid ORDER BY d2.districtname";
		// debugMessage($all_results_query);
		return $conn->fetchOne($all_results_query);;
	}
	/**
     * The open selling offers
     * @param int $this->getID()
     * @return The lattest fuel prices in a district
     */
	function getOpenSellOffers() {
		$conn = Doctrine_Manager::connection();
		$all_results_query = "SELECT 
					o.id as `id`, 
					o.createdby as `createdby`, 
					c.name as `Commodity`, 
					 if(ISNULL(cu.name), o.quantity,concat(o.quantity,' ',cu.name)) as `Quantity`, 
					 FORMAT(price,0) as `Price`, 
					 DATE_FORMAT(o.datecreated, '%b %d, %Y') as `Date Posted`, 
					 DATE_FORMAT(o.expirydate, '%b %d, %Y') as `Expiry Date`, 
					 o.contact as `Contact`, 
					 o.telephone as `Telephone`, 
					 l.name as `District` 
					 FROM offer o 
					 INNER JOIN commodity c ON o.commodityid = c.id 
					 LEFT JOIN commodityunit cu ON c.unitid = cu.id 
					 INNER JOIN location l
					 WHERE o.locationid = l.id AND o.locationid = '".$this->getID()."' AND o.offertype = '1' AND TO_DAYS(NOW()) > TO_DAYS(o.expirydate)  
					 ORDER BY o.expirydate DESC ";
		// debugMessage($all_results_query);
		return $conn->fetchAll($all_results_query);;
	}
	/**
	 * Return the statistics for the districts as a list 
	 * 
	 * @return Array 
	 * 
	 */
	function getListofStatistics() {
		$query = "SELECT l.`value`,lv.lookupvaluedescription AS `Statistic` FROM locationstatistic AS l 
                  INNER JOIN lookuptypevalue lv 
                  INNER JOIN (SELECT ls.`statistic`, MAX(ls.startdate) AS startdate FROM locationstatistic ls WHERE ls.`locationid` = '".$this->getID()."' GROUP BY ls.`statistic`) AS ls1 ON (ls1.startdate = l.`startdate` AND ls1.statistic = l.`statistic`) 
                WHERE l.statistic = lv.lookuptypevalue 
                  AND lv.lookuptypeid = 10 
                  AND l.locationid = '".$this->getID()."' 
                GROUP BY l.`statistic`
                ORDER BY lv.lookuptypevalue ";
		$conn = Doctrine_Manager::connection(); 
		$result = $conn->fetchAll($query); 
		$array = array(); 
		if (!$result) {
			return $array;  
		}
		
		// format the array so that a list can be generated
		
		foreach ($result as $line) {
			$array[$line['Statistic']] = $line['value']; 
		}
		
		return $array;  
	}
	# determine commodityid from searchable name
    function findByName($name, $type) {
    	$str_len = strlen($name);
    	trim($name);
    	$name = str_replace('district', '', strtolower($name));
    	
		$conn = Doctrine_Manager::connection();
		// query for check if location exists
		$unique_query = "SELECT id FROM location l WHERE l.name LIKE '%".$name."%' AND l.locationtype = '".$type."' ";
		$result = $conn->fetchOne($unique_query);
		// debugMessage($unique_query);
		// debugMessage($result);
		return $result; 
	}
}

?>