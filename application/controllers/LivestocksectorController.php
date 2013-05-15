<?php

class LivestocksectorController extends SecureController {	
	/**
	 * Get the name of the resource being accessed 
	 *
	 * @return String 
	 */
	function getResourceForACL() {
		return "Livestock Production"; 
	}
	
	public function livestockinfoAction(){
	}
	public function livestockinfolistAction(){
	}
	/* (non-PHPdoc)
	 * @see SecureController::getActionforACL()
	 */
	public function getActionforACL() {
		$action = strtolower($this->getRequest()->getActionName()); 
		
		if ($action == 'livestockinfo') {
			return ACTION_CREATE; 
		}
		if ($action == 'processlivestockinfo') {
			return ACTION_CREATE; 
		}
		
		if ($action == 'livestockinfolist') {
			return ACTION_LIST; 
		}
		return parent::getActionforACL(); 
	}
	
	public function createAction(){
		$this->_helper->layout->disableLayout();
	    $session = SessionWrapper::getInstance(); 
	    $this->_translate = Zend_Registry::get("translate"); 
	   
	    $formvalues = $this->_getAllParams();
	    //generate the start and end dates from the quarter
		
	    if(!isArrayKeyAnEmptyString('quarter', $formvalues)){
			$quarter_dates = getDatesForQuarter($formvalues['quarter']); 
			//add the start and end date to formvalues data
			$formvalues = array_merge_maintain_keys($formvalues, $quarter_dates); 
		}
	   
	    // add the parameters for updating the visit
		$this->_setParam('entityname', 'LivestockProduction'); 
		$this->_setParam('action', ACTION_CREATE);
		$submission_data = array();
		$counter = count($formvalues['commodityid']);
		for($i=0; $i<$counter; $i++){
				
				$submission_data[$i]['commodityid'] = $formvalues['commodityid'][$i];
				$submission_data[$i]['breedcost'] = $formvalues['breedcost'][$i];
				$submission_data[$i]['yield'] = $formvalues['yield'][$i];
				$submission_data[$i]['locationid'] = $formvalues['locationid'];
				$submission_data[$i]['collectedbyid'] = $formvalues['collectedbyid'];
				$submission_data[$i]['datecollected'] = changeDateFromPageToMySQLFormat($formvalues['datecollected']);
				$submission_data[$i]['startdate'] = changeDateFromPageToMySQLFormat($formvalues['startdate']);
				$submission_data[$i]['enddate'] = changeDateFromPageToMySQLFormat($formvalues['enddate']);
				$submission_data[$i]['datecollected'] = changeDateFromPageToMySQLFormat($formvalues['datecollected']);
				$submission_data[$i]['createdby'] = $session->getVar('userid');
				if(isNotAnEmptyString($formvalues['id'][$i])){
					$submission_data[$i]['id'] = $formvalues['id'][$i];
				}
		}
		
		// collection to be used to save submissions
		$livestocksector_collection = new Doctrine_Collection("LivestockProduction");
		
		// process each submission item and add to collection
		foreach ($submission_data as $thedata){
			$livestocksector = new LivestockProduction();
			if(isArrayKeyAnEmptyString('id', $thedata)){
				$livestocksector->processPost($thedata);
				
				if($livestocksector->isValid()) {
					$livestocksector_collection->add($livestocksector);
					
				} else {
				}
			} else {
				$livestocksector->populate($thedata['id']);
				$livestocksector->synchronizeWithArray($thedata, true);
				$livestocksector_collection->add($livestocksector);
			}
			
		}
		try {
			// save the collection of crop sectors
			$livestocksector_collection->save(); 
			// redirect to success page
			$this->_redirect($this->view->baseUrl('livestocksector/list/quarter/'.$formvalues['quarter']));
			
		} catch (Exception $e) {
			$session = SessionWrapper::getInstance(); 
			$session->setVar(FORM_VALUES, $formvalues);
			$session->setVar(ERROR_MESSAGE, $e->getMessage()); 
			// add the errors to the session
			
			$this->_redirect($this->view->baseUrl('livestocksector/create/quarter'.$formvalues['quarter']));
		}
	}
	/*
	 * process livestock informatoin
	 */
	public function processlivestockinfoAction(){
	$this->_helper->layout->disableLayout();
	    $session = SessionWrapper::getInstance(); 
	    $this->_translate = Zend_Registry::get("translate"); 
	   
	    $formvalues = $this->_getAllParams();
		
	    // add the parameters for updating the visit
		$this->_setParam('entityname', 'LivestockInformation'); 
		$this->_setParam('action', ACTION_CREATE);
		$submission_data = array();
		$counter = count($formvalues['commodityid']);
		for($i=0; $i<$counter; $i++){
				
				$submission_data[$i]['commodityid'] = $formvalues['commodityid'][$i];
				$submission_data[$i]['breedsource'] = $formvalues['breedsource'][$i];
				$submission_data[$i]['pests'] = $formvalues['pests'][$i];
				$submission_data[$i]['diseases'] = $formvalues['diseases'][$i];
				$submission_data[$i]['creditsource'] = $formvalues['creditsource'][$i];
				$submission_data[$i]['locationid'] = $formvalues['locationid'];
				$submission_data[$i]['createdby'] = $session->getVar('userid');
				if(isNotAnEmptyString($formvalues['id'][$i])){
					$submission_data[$i]['id'] = $formvalues['id'][$i];
				}
		}
		// collection to be used to save submissions
		$livestocksectorinfo_collection = new Doctrine_Collection("LivestockInformation");
		
		// process each submission item and add to collection
		foreach ($submission_data as $thedata){
			$livestocksectorinfo = new LivestockInformation();
			if(isArrayKeyAnEmptyString('id', $thedata)){
				$livestocksectorinfo->processPost($thedata);
				
				if($livestocksectorinfo->isValid()) {
					$livestocksectorinfo_collection->add($livestocksectorinfo);
					
				} else {
				}
			} else {
				$livestocksectorinfo->populate($thedata['id']);
				$livestocksectorinfo->synchronizeWithArray($thedata, true);
				$livestocksectorinfo_collection->add($livestocksectorinfo);
			}
			
		}

		try {
			// save the collection of crop sectors
			$livestocksectorinfo_collection->save(); 
			// redirect to success page
			$this->_redirect($this->view->baseUrl('livestocksector/livestockinfolist/locationid/'.encode($formvalues['locationid'])));
			
		} catch (Exception $e) {
			$session = SessionWrapper::getInstance(); 
			$session->setVar(FORM_VALUES, $formvalues);
			$session->setVar(ERROR_MESSAGE, $e->getMessage()); 
			// add the errors to the session
			
			$this->_redirect($this->view->baseUrl('livestocksector/livestockinfo/locationid'.encode($formvalues['locationid'])));
		}
	}
}

