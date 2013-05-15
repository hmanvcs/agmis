<?php

/**
 * Model for a commodity price submission
 *
 */

class CommodityPriceSubmission extends BaseEntity {
	
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('commoditypricesubmission');
		
		$this->hasColumn('datecollected','date', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('collectedbyid', 'integer', 11, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('sourceid', 'integer', 11, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('comments', 'string', 65535, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('status', 'enum', null, array('values' => array(0 => 'Saved', 1 => 'Approved', 2 => 'Rejected'), 'default' => 'Saved'));
		$this->hasColumn('approvedbyid', 'integer',null);
		$this->hasColumn('dateapproved','date',null);
	}
	
	function construct() {
		// initialize parent settings
		parent::construct();
		
		// define the date fields
		$this->addDateFields(array("datecollected","dateapproved"));
		
		// define the custom error messages
       	$this->addCustomErrorMessages(array(
       									"datecollected.notblank" => $this->translate->_("commodityprice_datecollected_error"),
       									"collectedbyid.notblank" => $this->translate->_("commodityprice_collectedby_error"),
       									"sourceid.notblank" => $this->translate->_("commodityprice_source_error"),
       									"comments.notblank" => $this->translate->_("commodityprice_comments_error")
       	       						)); 
	}
	/*
	 * 
	 */
	function setUp() {
		parent::setUp();
		// define the relationships
		$this->hasOne('PriceSource as source',
						 array(
								'local' => 'sourceid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('UserAccount as collectedby',
						 array(
								'local' => 'collectedbyid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('UserAccount as approvedby',
						 array(
								'local' => 'approvedbyid',
								'foreign' => 'id'
							)
					);
		$this->hasMany('CommodityPriceDetails as submissiondetails',
							array('local' => 'id',
									'foreign' => 'submissionid'
							)
						); 
	}
/*
	 * 
	 */
	function validate() {
		parent::validate();
		
		$conn = Doctrine_Manager::connection();
		
		# Validate that for new submissions, there is no existing submission for same date and market
		if(isEmptyString($this->getID())){
			$unique_query = "SELECT id FROM commoditypricesubmission WHERE sourceid = '".$this->getSourceID()."' AND datecollected = '".$this->getDateCollected()."' ";
			# debugMessage($unique_query);
			$result = $conn->fetchOne($unique_query);
			if(!isEmptyString($result)){ 
				$this->getErrorStack()->add("submission.unique",  sprintf($this->translate->_("commodityprice_unique_error"), changeMySQLDateToPageFormat($this->getDateCollected()), $this->getSource()->getName()));
			}
		}
		// exit();
	}
	/*
	 * 
	 */
	function processPost($formvalues){
		// Custom processing for integer and enum fields
		if(isArrayKeyAnEmptyString('dateapproved', $formvalues)){
			unset($formvalues['dateapproved']); 
		}
		if(isArrayKeyAnEmptyString('approvedbyid', $formvalues)){
			unset($formvalues['approvedbyid']); 
		}
		if(isArrayKeyAnEmptyString('status', $formvalues)){
			$formvalues['status'] = 'Saved'; 
		}
		
		// Loop through all submission details and remove any line having both amounts not specified
		if (array_key_exists('submissiondetails', $formvalues)) {
			$submissiondetails = $formvalues['submissiondetails']; 
			foreach ($submissiondetails as $key => $value) {
				// debugMessage($formvalues['submissiondetails'][$key]['retailprice']);
				if(isArrayKeyAnEmptyString('retailprice', $value)){
					$formvalues['submissiondetails'][$key]['retailprice'] = 0;
				}
				if(isArrayKeyAnEmptyString('wholesaleprice', $value)){
					$formvalues['submissiondetails'][$key]['wholesaleprice'] = 0;					
				}
				$formvalues['submissiondetails'][$key]['datecollected'] = changeDateFromPageToMySQLFormat($formvalues['datecollected']);
				$formvalues['submissiondetails'][$key]['sourceid'] = $formvalues['sourceid'];
			}
		}
		
		# debugMessage($formvalues);
		parent::processPost($formvalues);
	}
	/*
	 * 
	 */
	function searchKeyInPriceDetails($commodityid){ 		
		$matched_key = "";
		// convert all price details on this submission to an array 
		$searcharray = $this->getSubmissionDetails()->toArray();
		// Loop through each price detail and check if commodity exists 
		foreach ($searcharray as $key => $value){
			if($commodityid == $value['commodityid']){
				$matched_key = $key;				
				break; 	
			}		
		}
		return $matched_key;
	}
	/*
	 * 
	 */
	function getCurrentPriceDetails($pricecategoryid) {
		$conn = Doctrine_Manager::connection();
		
		# if viewing or editing existing prices, add the id of the submission
		$details_query = "";
		if(!isEmptyString($this->getID())){
			$details_query = "SELECT c.id AS id,
				c.name AS `name`, 
				c.categoryid AS `categoryid`,
				cc.name AS `category`,
				cp.pricecategoryid AS `pricecategoryid`,
				u.name AS `units`,
				if(ISNULL(cd.retailprice)OR (cd.retailprice = '0'), '---' ,cd.retailprice) AS retailprice, 
				if(ISNULL(cd.wholesaleprice) OR (cd.wholesaleprice = '0'), '---' ,cd.wholesaleprice) AS wholesaleprice,	
				cd.notes as notes	
				FROM commodity AS c
				Inner Join commoditypricecategory AS cp ON (c.id = cp.commodityid AND cp.pricecategoryid= '".$pricecategoryid."')
				Inner Join commoditycategory AS cc ON (c.categoryid = cc.id)
				Inner Join commodityunit AS u ON (c.unitid = u.id)
				Left Join commoditypricedetails AS cd ON (c.id = cd.commodityid  AND cd.submissionid = '".$this->getID()."')
				ORDER BY c.name";
		} else {
			$details_query = "SELECT c.id AS id,
				c.name AS `name`, 
				c.categoryid AS `categoryid`,
				cc.name AS `category`,
				cp.pricecategoryid AS `pricecategoryid`,
				u.name AS `units`
				FROM commodity AS c
				Inner Join commoditypricecategory AS cp ON (c.id = cp.commodityid AND cp.pricecategoryid= '".$pricecategoryid."')
				Inner Join commoditycategory AS cc ON (c.categoryid = cc.id)
				Inner Join commodityunit AS u ON (c.unitid = u.id)
				ORDER BY c.name";
		}
		
		return $conn->fetchAll($details_query);
	}
	/**
	 * Approve the Submission 
	 * 
	 * @return TRUE if submission has been Approved.
	 */
	function approve(){
		$this->setDateApproved(date("Y-m-d H:i:s"));
		try {
			# save the new email address
			$this->save();
			
			# Send user an notification to the users concerned
			
		} catch (Exception $e) {
			debugMessage("An error occured in Approving the Prices". $e->getMessage()); 
		}
      	
		return true;
	}
	/**
	 * The lattest price submission for a district 
	 * @return int the id of the latest price submission
	 */
	function getLatestPriceSubmissionIDForLocation($locationid, $pricecategory=2, $applicationtype=APPLICATION_LGMIS){
		$conn = Doctrine_Manager::connection();
		$query = "SELECT s.id AS id FROM commoditypricesubmission AS s INNER JOIN pricesource as p ON (s.sourceid = p.id) INNER JOIN location as l on (p.locationid = l.id) WHERE p.locationid = '".$locationid."' AND p.applicationtype = ".$applicationtype." ORDER BY s.datecollected DESC LIMIT 1";
		// debugMessage($query);
		$result = $conn->fetchOne($query);
		if(!isEmptyString($result)){
			return $this->populate($result);
		} else {
			return false;
		}
	}
}

?>