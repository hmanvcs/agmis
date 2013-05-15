<?php

class LgmisController extends IndexController {
	
	function preDispatch(){
		parent::preDispatch(); 
		
		$session = SessionWrapper::getInstance(); 
		$session->setVar('applicationtype', APPLICATION_LGMIS); 
	}
	
	function districtAction(){}
	function agriculturalchemicalAction(){}
	function businesssectorAction(){}
	function businesssectorsearchAction(){
		$this->_helper->redirector->gotoSimple("businesssector", "lgmis", 
    											$this->getRequest()->getModuleName(),
    											array_remove_empty(array_merge_maintain_keys($this->_getAllParams(), $this->getRequest()->getQuery())));
	}
	
	function touristattractionAction(){}
	function touristattractiondetailAction(){}
	function livestockproductionAction(){}
	function cropproductionAction(){}
	function fishproductionAction(){} 
	function handcraftsectorAction(){}	
	function loginAction(){}
}