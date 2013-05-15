<?php

class CropsectorController extends SecureController {	
	/**
	 * Get the name of the resource being accessed 
	 *
	 * @return String 
	 */
	function getResourceForACL() {
		return "Crop Production Information"; 
	}
	
	public function cropsectorinfoAction(){
	}
	public function cropsectorinfolistAction(){
	}
	/* (non-PHPdoc)
	 * @see SecureController::getActionforACL()
	 */
	public function getActionforACL() {
		$action = strtolower($this->getRequest()->getActionName()); 
		
		if ($action == 'cropsectorinfo') {
			return ACTION_CREATE; 
		}
		if ($action == 'processcropsectorinfo') {
			return ACTION_CREATE; 
		}
		
		if ($action == 'cropsectorinfolist') {
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
		$this->_setParam('entityname', 'CropProduction'); 
		$this->_setParam('action', ACTION_CREATE);
		$submission_data = array();
		$counter = count($formvalues['commodityid']);
		for($i=0; $i<$counter; $i++){
				
				$submission_data[$i]['commodityid'] = $formvalues['commodityid'][$i];
				$submission_data[$i]['seedcost'] = $formvalues['seedcost'][$i];
				$submission_data[$i]['yieldperacre'] = $formvalues['yieldperacre'][$i];
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
		$cropsector_collection = new Doctrine_Collection("CropProduction");
		
		// process each submission item and add to collection
		foreach ($submission_data as $thedata){
			$cropsector = new CropProduction();
			if(isArrayKeyAnEmptyString('id', $thedata)){
				$cropsector->processPost($thedata);
				
				if($cropsector->isValid()) {
					$cropsector_collection->add($cropsector);
					
				} else {
				}
			} else {
				$cropsector->populate($thedata['id']);
				$cropsector->synchronizeWithArray($thedata, true);
				$cropsector_collection->add($cropsector);
			}
			
		}
		
		try {
			// save the collection of crop sectors
			$cropsector_collection->save(); 
			// redirect to success page
			$this->_redirect($this->view->baseUrl('cropsector/list/quarter/'.$formvalues['quarter']));
		} catch (Exception $e) {
			$session = SessionWrapper::getInstance(); 
			$session->setVar(FORM_VALUES, $formvalues);
			$session->setVar(ERROR_MESSAGE, $e->getMessage()); 
			// add the errors to the session
			
			$this->_redirect($this->view->baseUrl('cropsector/create/quarter'.$formvalues['quarter']));
		}
	}
	
	/*
	 * Processing for cropsector information
	 */
	public function processcropsectorinfoAction(){
		$this->_helper->layout->disableLayout();
	    $session = SessionWrapper::getInstance(); 
	    $this->_translate = Zend_Registry::get("translate"); 
	   
	    $formvalues = $this->_getAllParams();
	    
	    // add the parameters for updating the crop sector infor
		$this->_setParam('entityname', 'CropSectorInformation'); 
		$this->_setParam('action', ACTION_CREATE);
		$submission_data = array();
		$counter = count($formvalues['commodityid']);
		for($i=0; $i<$counter; $i++){
				
			$submission_data[$i]['commodityid'] = $formvalues['commodityid'][$i];
				$submission_data[$i]['seedsource'] = $formvalues['seedsource'][$i];
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
		$cropsectorinfo_collection = new Doctrine_Collection("CropSectorInformation");
		
		// process each submission item and add to collection
		foreach ($submission_data as $thedata){
			$cropsectorinfo = new CropSectorInformation();
			if(isArrayKeyAnEmptyString('id', $thedata)){
				$cropsectorinfo->processPost($thedata);
				
				if($cropsectorinfo->isValid()) {
					$cropsectorinfo_collection->add($cropsectorinfo);
					
				} else {
				}
			} else {
				$cropsectorinfo->populate($thedata['id']);
				$cropsectorinfo->synchronizeWithArray($thedata, true);
				$cropsectorinfo_collection->add($cropsectorinfo);
			}
			
		}
		
		try {
			// save the collection of crop sectors
			$cropsectorinfo_collection->save(); 
			// redirect to success page
			$this->_redirect($this->view->baseUrl('cropsector/cropsectorinfolist/locationid/'.encode($formvalues['locationid'])));
		} catch (Exception $e) {
			$session = SessionWrapper::getInstance(); 
			$session->setVar(FORM_VALUES, $formvalues);
			$session->setVar(ERROR_MESSAGE, $e->getMessage()); 
			// add the errors to the session
			$this->_redirect($this->view->baseUrl('cropsector/cropsectorinfo/locationid/'.encode($formvalues['locationid'])));
		}
	}

}

