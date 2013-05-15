<?php

class ApiController extends IndexController  {

    function indexAction() {
    	$this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    function pricesAction(){
    	// disable rendering of the view and layout 
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    
    }
}

