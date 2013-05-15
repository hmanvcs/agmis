<?php

class FishingsectorController extends SecureController {	
	/**
	 * Get the name of the resource being accessed 
	 *
	 * @return String 
	 */
	function getResourceForACL() {
		return "Fishing Sector Information"; 
	}
	public function getActionforACL() {
		$action = strtolower($this->getRequest()->getActionName()); 
		if ($action == 'populatedistrict') {
			return ACTION_VIEW; 
		}
		return parent::getActionforACL(); 
	}
	function populatedistrictAction(){
    	$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
		// debugMessage($this->_getAllParams());
		$landingsite = new LandingSite();
		$landingsite->populate($this->_getParam('landingsiteid'));
		$resultarray = array(
    				'districtid' => $landingsite->getDistrictID()
    			);
    	echo json_encode($resultarray);
    }
}

