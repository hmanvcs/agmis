<?php
class GraphController extends IndexController {
	
	function commoditypriceAction() {
		
    }
	function commoditypriceexportAction(){
    	$this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    
	   $all_results_query = "SELECT d.id, d.datecollected, c.`name` AS Commodity, CONCAT(MONTH(d.datecollected),YEAR(d.datecollected)) AS monthyear,
		DATE_FORMAT(d.datecollected, '%b %Y') AS `Month`, ROUND(AVG(d.retailprice), - 2) AS `RetailPrice`, ROUND(AVG(d.wholesaleprice), - 2) AS `WholesalePrice` 
		FROM commoditypricedetails AS d INNER JOIN commodity AS c ON (d.`commodityid` = c.`id`) 
		INNER JOIN pricesource AS p ON (d.sourceid = p.id) INNER JOIN location AS l 
		WHERE p.locationid = l.id AND TO_DAYS(d.datecollected) >= TO_DAYS(DATE_SUB(NOW(), INTERVAL 6 MONTH)) AND d.`commodityid` IN (7, 29, 14, 32, 4) 
		GROUP BY YEAR(d.datecollected), MONTH(d.datecollected), d.`commodityid` 
		ORDER BY c.name, d.datecollected ASC ";
	    // debugMessage($all_results_query);
	
		$conn = Doctrine_Manager::connection(); 
		$result = $conn->fetchAll($all_results_query);
		// debugMessage($result);
		$result_count = count($result);
		if($result_count == 0){
    		echo "RESULT_NULL";
    	} else {
    		$feed = '';
    		foreach ($result as $line){
		    	$feed .= '<item>';
	    		$feed .= '<id>'.$line['id'].'</id>';
	    		$feed .= '<datecollected>'.$line['datecollected'].'</datecollected>';
		    	$feed .= '<Commodity>'.$line['Commodity'].'</Commodity>';
		    	$feed .= '<monthyear>'.$line['monthyear'].'</monthyear>';
		    	$feed .= '<Month>'.$line['Month'].'</Month>';
		    	$feed .= '<RetailPrice>'.$line['RetailPrice'].'</RetailPrice>';
			    $feed .= '</item>';
			}
			echo '<?xml version="1.0" encoding="UTF-8" ?><items>'.$feed.'</items>';
    	}
    }
	function fuelpriceAction() {
		
    }
    function fuelpriceexportAction(){
    	$this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    
	    $all_results_query = "SELECT d.id, d.datecollected, c.`name` AS Commodity, CONCAT(MONTH(d.datecollected),YEAR(d.datecollected)) AS monthyear,
		DATE_FORMAT(d.datecollected, '%b %Y') AS `Month`, ROUND(AVG(d.retailprice), - 2) AS `RetailPrice` 
		FROM commoditypricedetails AS d INNER JOIN commodity AS c ON (d.`commodityid` = c.`id`) 
		INNER JOIN pricesource AS p ON (d.sourceid = p.id) INNER JOIN location AS l 
		WHERE p.locationid = l.id AND TO_DAYS(d.datecollected) >= TO_DAYS(DATE_SUB(NOW(), INTERVAL 6 MONTH)) AND d.`commodityid` IN (95, 96, 97) 
		GROUP BY YEAR(d.datecollected), MONTH(d.datecollected), d.`commodityid` 
		ORDER BY c.name, d.datecollected ASC ";
		// debugMessage($all_results_query);
	
		$conn = Doctrine_Manager::connection(); 
		$result = $conn->fetchAll($all_results_query);
		// debugMessage($result);
		$result_count = count($result);
		if($result_count == 0){
    		echo "RESULT_NULL";
    	} else {
    		$feed = '';
    		foreach ($result as $line){
		    	$feed .= '<item>';
	    		$feed .= '<id>'.$line['id'].'</id>';
	    		$feed .= '<datecollected>'.$line['datecollected'].'</datecollected>';
		    	$feed .= '<Commodity>'.$line['Commodity'].'</Commodity>';
		    	$feed .= '<monthyear>'.$line['monthyear'].'</monthyear>';
		    	$feed .= '<Month>'.$line['Month'].'</Month>';
		    	$feed .= '<RetailPrice>'.$line['RetailPrice'].'</RetailPrice>';
			    $feed .= '</item>';
			}
			echo '<?xml version="1.0" encoding="UTF-8" ?><items>'.$feed.'</items>';
    	}
    }
    function inputpriceAction() {
    	
    }
	function marketanalysisreportAction() {
    }
	function commodityAction() {
    }
    
    function currentmarketpricesAction(){}
	function currentfuelpricesAction(){}
	function openselloffersAction(){}
	function currentorganicpricesAction(){}
	function currentinputpricesAction(){}
	
	function allpricesexportAction(){
		$this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    
		$filter = "";
		$filter2 = "";
		if (!isEmptyString($this->_getParam('summary'))) {
			$filter = " AND d.`commodityid` IN (7, 29, 14, 32, 4, 22, 34,55, 156) "; 
			$filter2 = " AND cp.`commodityid` IN (7, 29, 14, 32, 4, 22, 34,55, 156) ";
		}
		$all_results_query = "SELECT 
			  d.datecollected AS datecollected,
			  cd.`name`,
			  FORMAT(ROUND(AVG(d.retailprice), -1), 0) AS `RetailPrice`,
			  FORMAT(ROUND(AVG(d.wholesaleprice), -1), 0) AS `WholesalePrice`
			  FROM
			  commoditypricedetails AS d 
			  INNER JOIN commodity cd ON d.`commodityid` = cd.`id`
			  INNER JOIN 
			    (SELECT 
			      MAX(cp.datecollected) AS datecollected
			    FROM
			      commoditypricedetails cp 
			      INNER JOIN commoditypricesubmission AS cs1 
			        ON (
			          cp.`submissionid` = cs1.`id` 
			          AND cs1.`status` = 'Approved'
			        ) 
			      INNER JOIN pricesource AS p 
			        ON (
			          cp.sourceid = p.id 
			          AND p.`applicationtype` = 0
			        ) 
			    WHERE cp.`pricecategoryid` = 2 ".$filter2."  
			   	) AS d2 
			    ON (
			      d.`datecollected` = d2.datecollected
			    ) 
			WHERE d.`pricecategoryid` = 2 ".$filter."
			GROUP BY d.`commodityid`
			ORDER BY cd.name ";

		// debugMessage($all_results_query);
		$conn = Doctrine_Manager::connection(); 
		$result = $conn->fetchAll($all_results_query);
		// debugMessage($result);
		$result_count = count($result);
		if($result_count == 0){
    		echo "RESULT_NULL";
    	} else {
    		$feed = '';
    		foreach ($result as $line){
		    	$feed .= '<item>';
	    		$feed .= '<datecollected>'.$line['datecollected'].'</datecollected>';
		    	$feed .= '<name>'.$line['name'].'</name>';
		    	$feed .= '<RetailPrice>'.$line['RetailPrice'].'</RetailPrice>';
		    	$feed .= '<WholesalePrice>'.$line['WholesalePrice'].'</WholesalePrice>';
			    $feed .= '</item>';
			}
			echo '<?xml version="1.0" encoding="UTF-8" ?><items>'.$feed.'</items>';
    	}
	}
	 
}

