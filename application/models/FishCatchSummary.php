<?php

/**
 * Model for fish catch summary records 
 *
 */

class FishCatchSummary extends BaseEntity {

	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
				
		$this->setTableName('fishcatchsummary');
		
		$this->hasColumn('landingsiteid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('districtid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('recordedbyid', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('daterecorded','date', null, array('notnull' => true, 'notblank' => true));
	} 
	
	function construct() {
		// initialize parent settings
		parent::construct();
		
		// define the date fields
		$this->addDateFields(array("daterecorded"));
		
		// define the custom error messages
       	$this->addCustomErrorMessages(array(
       									"landingsiteid.notblank" => $this->translate->_("fishingsector_landingsiteid_error"),
       									"districtid.notblank" => $this->translate->_("fishingsector_districtid_error"),
       									"recordedbyid.notblank" => $this->translate->_("fishingsector_recordededbyid_error"),
       									"daterecorded.notblank" => $this->translate->_("fishingsector_daterecorded_error")
       	       						)); 
	}
	
	public function setUp() {
		parent::setUp(); 
		
		$this->hasOne('District as district',
						 array(
								'local' => 'districtid',
								'foreign' => 'id'
							)
					); 
		
		$this->hasOne('LandingSite as landingsite',
						 array(
								'local' => 'landingsiteid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('UserAccount as recordedby',
						 array(
								'local' => 'recordedbyid',
								'foreign' => 'id'
							)
					);
		$this->hasMany('FishCatchDetail as fishcatchdetails',
							array('local' => 'id',
									'foreign' => 'catchsummaryid'
							)
						); 
	} 
	
	/**
	 * Model validation  
	 */
	function validate() {
		parent::validate();
		
		$conn = Doctrine_Manager::connection();
		# Validate that for new submissions, there is no existing submission for same date and market
		$custom_query = "";
		if(!isEmptyString($this->getID())){
			$custom_query = " AND id <> '".$this->getID()."'";
		}
		$unique_query = "SELECT id FROM fishcatchsummary WHERE daterecorded = '".$this->getDateRecorded()."' AND landingsiteid = '".$this->getLandingSiteID()."' ".$custom_query;
		# debugMessage($unique_query);
		$result = $conn->fetchOne($unique_query);
		if(!isEmptyString($result)){ 
			$this->getErrorStack()->add("submission.unique",  sprintf($this->translate->_("fishingsector_unique_error"), changeMySQLDateToPageFormat($this->getDateRecorded()), $this->getLandingSite()->getName()));
		}
	}
	/**
	 * preprocess model data  
	 */
	function processPost($formvalues){
		// debugMessage($formvalues);
		$details_array = array();
		for($i=1; $i<=$formvalues['rowcount']; $i++){
			// make all empty values zeros
			if (isArrayKeyAnEmptyString('mputacount_'.$i, $formvalues)) {
				$formvalues['mputacount_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('mputaweight_'.$i, $formvalues)) {
				$formvalues['mputaweight_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('ngegecount_'.$i, $formvalues)) {
				$formvalues['ngegecount_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('ngegeweight_'.$i, $formvalues)) {
				$formvalues['ngegeweight_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('mukenecount_'.$i, $formvalues)) {
				$formvalues['mukenecount_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('mukeneweight_'.$i, $formvalues)) {
				$formvalues['mukeneweight_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('mambacount_'.$i, $formvalues)) {
				$formvalues['mambacount_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('mambaweight_'.$i, $formvalues)) {
				$formvalues['mambaweight_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('malecount_'.$i, $formvalues)) {
				$formvalues['malecount_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('maleweight_'.$i, $formvalues)) {
				$formvalues['maleweight_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('othertilapiacount_'.$i, $formvalues)) {
				$formvalues['othertilapiacount_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('othertilapiaweight_'.$i, $formvalues)) {
				$formvalues['othertilapiaweight_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('othercount_'.$i, $formvalues)) {
				$formvalues['othercount_'.$i] = 0;
			}
			if (isArrayKeyAnEmptyString('otherweight_'.$i, $formvalues)) {
				$formvalues['otherweight_'.$i] = 0;
			}
			// store details into details array
			$details_array[md5($i)]['boatid'] = $formvalues['boatid_'.$i];
			$details_array[md5($i)]['nettype'] = $formvalues['nettype_'.$i];
			$details_array[md5($i)]['hooksize'] = $formvalues['hooksize_'.$i];
			$details_array[md5($i)]['hookno'] = $formvalues['hookno_'.$i];
			$details_array[md5($i)]['mputaid'] = $formvalues['mputaid_'.$i];
			$details_array[md5($i)]['mputacount'] = $formvalues['mputacount_'.$i];
			$details_array[md5($i)]['mputaweight'] = $formvalues['mputaweight_'.$i];
			$details_array[md5($i)]['ngegeid'] = $formvalues['ngegeid_'.$i];
			$details_array[md5($i)]['ngegecount'] = $formvalues['ngegecount_'.$i];
			$details_array[md5($i)]['ngegeweight'] = $formvalues['ngegeweight_'.$i];
			$details_array[md5($i)]['mukeneid'] = $formvalues['mukeneid_'.$i];
			$details_array[md5($i)]['mukenecount'] = $formvalues['mukenecount_'.$i];
			$details_array[md5($i)]['mukeneweight'] = $formvalues['mukeneweight_'.$i];
			$details_array[md5($i)]['mambaid'] = $formvalues['mambaid_'.$i];
			$details_array[md5($i)]['mambacount'] = $formvalues['mambacount_'.$i];
			$details_array[md5($i)]['mambaweight'] = $formvalues['mambaweight_'.$i];
			$details_array[md5($i)]['maleid'] = $formvalues['maleid_'.$i];
			$details_array[md5($i)]['malecount'] = $formvalues['malecount_'.$i];
			$details_array[md5($i)]['maleweight'] = $formvalues['maleweight_'.$i];
			$details_array[md5($i)]['othertilapiaid'] = $formvalues['othertilapiaid_'.$i];
			$details_array[md5($i)]['othertilapiacount'] = $formvalues['othertilapiacount_'.$i];
			$details_array[md5($i)]['othertilapiaweight'] = $formvalues['othertilapiaweight_'.$i];
			$details_array[md5($i)]['otherid'] = $formvalues['otherid_'.$i];
			$details_array[md5($i)]['othercount'] = $formvalues['othercount_'.$i];
			$details_array[md5($i)]['otherweight'] = $formvalues['otherweight_'.$i];
			
			if(isEmptyString($details_array[md5($i)]['boatid']) || isEmptyString($details_array[md5($i)]['nettype'])){
				unset($details_array[md5($i)]);
			}
		}
		
		// debugMessage($details_array);
		$formvalues['fishcatchdetails'] = $details_array;
		// debugMessage($formvalues); exit();	
		
		parent::processPost($formvalues);
	}	
	
	
}

?>