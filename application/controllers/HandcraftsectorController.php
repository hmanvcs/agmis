<?php

class HandcraftsectorController extends SecureController {	
	/**
	 * Get the name of the resource being accessed 
	 *
	 * @return String 
	 */
	function getResourceForACL() {
		return "Handcraft Sector Information"; 
	}
	
	public function handcraftsectorinfoAction(){
	}
	public function handcraftsectorinfolistAction(){
	}
	/* (non-PHPdoc)
	 * @see SecureController::getActionforACL()
	 */
	public function getActionforACL() {
		$action = strtolower($this->getRequest()->getActionName()); 
		
		if ($action == 'handcraftsectorinfo') {
			return ACTION_CREATE; 
		}
		if ($action == 'processhandcraftsectorinfo') {
			return ACTION_CREATE; 
		}
		
		if ($action == 'handcraftsectorinfolist') {
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
		$this->_setParam('entityname', 'HandCraftProduction'); 
		$this->_setParam('action', ACTION_CREATE);
		$submission_data = array();
		$counter = count($formvalues['commodityid']);
		for($i=0; $i<$counter; $i++){
				
				$submission_data[$i]['commodityid'] = $formvalues['commodityid'][$i];
				$submission_data[$i]['retail'] = $formvalues['retail'][$i];
				$submission_data[$i]['wholesale'] = $formvalues['wholesale'][$i];
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
		$handcraftsector_collection = new Doctrine_Collection("HandCraftProduction");
		
		// process each submission item and add to collection
		foreach ($submission_data as $thedata){
			$handcraftsector = new HandCraftProduction();
			if(isArrayKeyAnEmptyString('id', $thedata)){
				$handcraftsector->processPost($thedata);
				
				if($handcraftsector->isValid()) {
					$handcraftsector_collection->add($handcraftsector);
					
				} else {
				}
			} else {
				$handcraftsector->populate($thedata['id']);
				$handcraftsector->synchronizeWithArray($thedata, true);
				$handcraftsector_collection->add($handcraftsector);
			}
			
		}
		
		try {
			// save the collection of handcraft sectors
			$handcraftsector_collection->save(); 
			// redirect to success page
			$this->_redirect($this->view->baseUrl('handcraftsector/list/quarter/'.$formvalues['quarter']));
		} catch (Exception $e) {
			$session = SessionWrapper::getInstance(); 
			$session->setVar(FORM_VALUES, $formvalues);
			$session->setVar(ERROR_MESSAGE, $e->getMessage()); 
			// add the errors to the session
			
			$this->_redirect($this->view->baseUrl('handcraftsector/create/quarter'.$formvalues['quarter']));
		}
	}
	
	/*
	 * Processing for handcraft information
	 */
	public function processhandcraftsectorinfoAction(){
		$this->_helper->layout->disableLayout();
	    $session = SessionWrapper::getInstance(); 
	    $this->_translate = Zend_Registry::get("translate"); 
	   
	    $formvalues = $this->_getAllParams();
	    //debugMessage($formvalues);
	   // exit();
	    
	    // add the parameters for updating the handcraft sector info
		$this->_setParam('entityname', 'HandCraftInformation'); 
		$this->_setParam('action', ACTION_CREATE);
		$submission_data = array();
		$counter = count($formvalues['commodityid']);
		for($i=0; $i<$counter; $i++){
				
			$submission_data[$i]['commodityid'] = $formvalues['commodityid'][$i];
				$submission_data[$i]['materialsource'] = $formvalues['materialsource'][$i];
				$submission_data[$i]['materialtype'] = $formvalues['materialtype'][$i];
				$submission_data[$i]['market'] = $formvalues['market'][$i];
				$submission_data[$i]['creditsource'] = $formvalues['creditsource'][$i];
				$submission_data[$i]['locationid'] = $formvalues['locationid'];
				$submission_data[$i]['createdby'] = $session->getVar('userid');
				if(isNotAnEmptyString($formvalues['id'][$i])){
					$submission_data[$i]['id'] = $formvalues['id'][$i];
				}
		}
		// collection to be used to save submissions
		$handcraftinfo_collection = new Doctrine_Collection("HandCraftInformation");
		
		// process each submission item and add to collection
		foreach ($submission_data as $thedata){
			$handcraftinfo = new HandCraftInformation();
			if(isArrayKeyAnEmptyString('id', $thedata)){
				$handcraftinfo->processPost($thedata);
				
				if($handcraftinfo->isValid()) {
					$handcraftinfo_collection->add($handcraftinfo);
					
				} else {
				}
			} else {
				$handcraftinfo->populate($thedata['id']);
				$handcraftinfo->synchronizeWithArray($thedata, true);
				$handcraftinfo_collection->add($handcraftinfo);
			}
			
		}
		
		try {
			// save the collection of handcraft sector information
			$handcraftinfo_collection->save(); 
			// redirect to success page
			$this->_redirect($this->view->baseUrl('handcraftsector/handcraftsectorinfolist/locationid/'.encode($formvalues['locationid'])));
		} catch (Exception $e) {
			$session = SessionWrapper::getInstance(); 
			$session->setVar(FORM_VALUES, $formvalues);
			$session->setVar(ERROR_MESSAGE, $e->getMessage()); 
			// add the errors to the session
			$this->_redirect($this->view->baseUrl('handcraftsector/handcraftsectorinfo/locationid/'.encode($formvalues['locationid'])));
		}
	}
}

