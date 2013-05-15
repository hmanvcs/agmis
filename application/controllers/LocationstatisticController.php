<?php

class LocationstatisticController extends IndexController {

	function getResourceForACL() {
		return "Location Statistic";
	}
		
	public function multipleAction(){
	}
	public function processmultipleAction(){
		
	 	$this->_helper->layout->disableLayout();
	    $session = SessionWrapper::getInstance(); 
	    $this->_translate = Zend_Registry::get("translate"); 
	    
	    $formvalues = $this->_getAllParams();
	   
	    // add the parameters for updating the visit
		$this->_setParam('entityname', 'LocationStatistic'); 
		$this->_setParam('action', ACTION_CREATE);
		$submission_data = array();
		
		for($i=1; $i<=$formvalues['counter']; $i++){
			if(!isArrayKeyAnEmptyString('statistic_'.$i, $formvalues)){
				
				$submission_data[$i]['startdate'] = changeDateFromPageToMySQLFormat($formvalues['enddate']);
				$submission_data[$i]['enddate'] = changeDateFromPageToMySQLFormat($formvalues['enddate']);
				$submission_data[$i]['locationid'] = $formvalues['locationid'];
				$submission_data[$i]['statistic'] = $formvalues['statistic_'.$i];
				$submission_data[$i]['value'] = $formvalues['value_'.$i];
				//$submission_data[$i]['createdby'] = $session->getVar('userid');
				$submission_data[$i]['createdby'] = 1;
				if(!isArrayKeyAnEmptyString('id_'.$i, $formvalues)){
					$submission_data[$i]['id'] = $formvalues['id_'.$i];
				}
			} 
		}
		// debugMessage($submission_data);
		//exit();
		// collection to be used to save submissions
		$locationstatistic_collection = new Doctrine_Collection("LocationStatistic");
		// process each submission it and add to collection
		
		foreach ($submission_data as $thedata){
			$locationstatistic = new LocationStatistic();
			if(isArrayKeyAnEmptyString('id', $thedata)){
				$locationstatistic->processPost($thedata);
				if($locationstatistic->isValid()) {
					$locationstatistic_collection->add($locationstatistic);
				}
			} else {
				$locationstatistic->populate($thedata['id']);
				$locationstatistic->synchronizeWithArray($thedata, true);
				$locationstatistic_collection->add($locationstatistic);
			}
			
		}
		try {
			// save the collection of statistics
			$locationstatistic_collection->save(); 
			// redirect to success page
			$this->_redirect($this->view->baseUrl('locationstatistic/list'));
		} catch (Exception $e) {
			$session = SessionWrapper::getInstance(); 
			$session->setVar(FORM_VALUES, $formvalues);
			$session->setVar(ERROR_MESSAGE, $e->getMessage()); 
			// add the errors to the session
			$this->_redirect($this->view->baseUrl('locationstatistic/multiple'));
		}
	}
}

