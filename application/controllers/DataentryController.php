<?php
class DataentryController extends SecureController   {
	
	function getResourceForACL() {
		return "Dashboard"; 
	}
	
	function getActionforACL() {
		return ACTION_VIEW; 
	}
    
}

