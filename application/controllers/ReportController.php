<?php
class ReportController extends SecureController   {
	
	/**
	 * Get the name of the resource being accessed 
	 *
	 * @return String 
	 */
	function getActionforACL() {
		return ACTION_VIEW;
	}
	/**
	 * Get the name of the resource being accessed 
	 *
	 * @return String 
	 */
	function getResourceForACL() {
		$resource = strtolower($this->getRequest()->getActionName()); 
		
		if ($resource == "marketprices") {
			return "Market Prices Report";
		}
		if ($resource == "marketpricesdetail") {
			return "Market Prices Detail Report";
		}
		if ($resource == "pricecomparison") {
			return "Market Price Comparison Report";
		}
		if ($resource == "averagepricesummary") {
			return "Average Market Prices Summary Report";
		}
		if ($resource == "aggregatedpricedetail") {
			return "Aggregated Market Prices Detail Report";
		}
		if ($resource == "currentmarketprices") {
			return "Current Market Prices Report";
		}
		if ($resource == "currentfuelprices") {
			return "Current Fuel Prices Report";
		}
		if ($resource == "historicalmarketprices") {
			return "Historical Market Prices Report";
		}
		if ($resource == "historicalfuelprices") {
			return "Historical Fuel Prices Report";
		}
		if ($resource == "averageboats") {
			//return "Average Boats Reports";
			return "Historical Fuel Prices Report";
		}
		if ($resource == "averagecatch") {
			//return "Average Boats Reports";
			return "Historical Fuel Prices Report";
		}
		return parent::getResourceForACL(); 
	}
	function marketpricesAction() {
		
    }
	function marketpricesdetailAction() {
		
    }
    function pricecomparisonAction() {
    	
    }
	function averagepricesummaryAction() {
    	
    }
	function aggregatedpricedetailAction() {
    	
    }
	function currentmarketpricesAction() {
    	
    }
	function currentfuelpricesAction() {
    	
    }
	function historicalmarketpricesAction() {
    	
    }
	function historicalfuelpricesAction() {
    	
    }
	function averagecatchAction() {
    	
    }
	function averageboatsAction() {
    	
    }
	/**
     * Pre-processing for all actions
     *
     * - Disable the layout when displaying printer friendly pages 
     *
     */
    function preDispatch(){
		
		parent::preDispatch();
    	// disable rendering of the layout so that we can just echo the AJAX output
    	if(!isEmptyString($this->_getParam(EXPORT_TO_EXCEL))) { 
			
			// disable rendering of the view and layout so that we can just echo the AJAX output
			$this->_helper->layout->disableLayout();			
	
			// required for IE, otherwise Content-disposition is ignored
			if(ini_get('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}
			
			$response = $this->getResponse();
			
			# This line will stream the file to the user rather than spray it across the screen
			$response->setHeader("Content-type", "application/vnd.ms-excel");
			
			# replace excelfile.xls with whatever you want the filename to default to
			$response->setHeader("Content-Disposition", "attachment;filename=".time().rand(1, 10).".xls");
			$response->setHeader("Expires", 0);
			$response->setHeader("Cache-Control", "private");
			session_cache_limiter("public");
		} 
    } 
}

