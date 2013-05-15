<?php

class FeedController extends IndexController  {

    function indexAction() {
    	// disable rendering of the view and layout 
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    
    	// $session = SessionWrapper::getInstance();
    	// debugMessage($this->_getAllParams());
    	
    	$conn = Doctrine_Manager::connection(); 
    	$query = "SELECT 
					d.datecollected as date, 
					REPLACE(d2.name,' Market','')as market,
					c.name as commodity,
					cu.name as unit,
					d.retailprice AS retailprice, 
					d.wholesaleprice AS wholesaleprice
					FROM commoditypricedetails AS d 
					INNER JOIN commodity AS c ON (d.`commodityid` = c.id)
					LEFT JOIN commodityunit AS cu ON (c.unitid = cu.id)
					INNER JOIN (
					SELECT cp.sourceid, 
					MAX(cp.datecollected) AS datecollected, 
					p.name FROM commoditypricedetails cp 
					INNER JOIN commoditypricesubmission AS cs1 ON (cp.`submissionid` = cs1.`id` 
					AND cs1.`status` = 'Approved') 
					INNER JOIN pricesource AS p ON (cp.sourceid = p.id AND p.`applicationtype` = 0 )
					WHERE cp.`pricecategoryid` = 2 GROUP BY cp.sourceid) AS d2 
					ON (d.`sourceid` = d2.sourceid AND d.`datecollected` = d2.datecollected) 
					WHERE d.`pricecategoryid` = 2 AND d.retailprice > 0 AND d.wholesaleprice > 0 AND date(d.datecollected) >= date(now()-interval 30 day) ORDER BY d2.name";
    	
    	// debugMessage($query);
    	$result = $conn->fetchAll($query);
    	//debugMessage($result);
    	
    	$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
	    $rssfeed .= '<rss version="2.0">';
	    $rssfeed .= '<channel>';
	    $rssfeed .= '<title>Latest Prices - '.date("F j, Y g:i a").'</title>';
	    $rssfeed .= '<link>http://infotradeuganda.com/feed/</link>';
	    $rssfeed .= '<description>The latest retail and wholesale prices for commodities in Uganda provided by infotradeuganda.com</description>';
	    $rssfeed .= '<language>en-us</language>';
	    $rssfeed .= '<copyright>Copyright (C) 2010 infotradeuganda.com</copyright>';
	    
		$timestamp_today = '';
		$timestamp_onemonthago = '';
	    foreach ($result as $line){
	    	$rssfeed .= '<item>';
	    	$rssfeed .= '<market>' .$line['market']. '</market>';
	    	$rssfeed .= '<product>' .$line['commodity']. '</product>';
	    	$rssfeed .= '<unit>' .$line['unit']. '</unit>';
	    	$rssfeed .= '<date>' .$line['date']. '</date>';
	    	$rssfeed .= '<retailPrice>' .$line['retailprice']. '</retailPrice>';
		    $rssfeed .= '<wholesalePrice>' .$line['wholesaleprice']. '</wholesalePrice>';
		    $rssfeed .= '</item>';
	    }
	    
	    $rssfeed .= '</channel>';
	    $rssfeed .= '</rss>';
	    echo $rssfeed;
    }
    
    function pricesAction(){
    	// disable rendering of the view and layout 
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    
    	// $session = SessionWrapper::getInstance();
    	$formvalues = $this->_getAllParams();
    	// debugMessage($formvalues);
    	
    	$where_query = "";
    	$bundled = true;
    	$commodityquery = false;
    	if(count($formvalues) == 3){
    		echo "NULL_PARAMETER_LIST";
    		exit();
    	}
    	if(isArrayKeyAnEmptyString('commodity', $formvalues) && isArrayKeyAnEmptyString('commodityid', $formvalues) && $this->_getParam('range') != 'all'){
    		echo "COMMODITY_NULL";
    		exit();
    	}
    	# commodity query
    	if((!isArrayKeyAnEmptyString('commodityid', $formvalues) || !isArrayKeyAnEmptyString('commodity', $formvalues)) && $this->_getParam('range') != 'all'){
    		$com = new Commodity();
    		// commodityid specified
    		if(!isEmptyString($this->_getParam('commodityid'))){
	    		$com->populate($formvalues['commodityid']);
	    		// debugMessage($com->toArray());
	    		if(isEmptyString($com->getID()) || !is_numeric($formvalues['commodityid'])){
	    			echo "COMMODITY_INVALID";
	    			exit();
	    		}
	    		$comid = $formvalues['commodityid'];
	    		$where_query .= " AND d.commodityid = '".$comid."' ";
    		}
    		// commodty name specified
    		if(!isEmptyString($this->_getParam('commodity'))){
    			$searchstring = $formvalues['commodity'];
    			$islist = false;
	    		if(strpos($searchstring, ',') !== false) {
					$islist = true;
				}
				if(!$islist){
	    			$comid = $com->findByName($searchstring);
	    			$comid_count = count($comid);
	    			if($comid_count == 0){
		    			echo "COMMODITY_INVALID";
		    			exit();
		    		}
		    		if($comid_count == 1){
		    			$where_query .= " AND d.commodityid = '".$comid[0]['id']."' ";
		    		}
	    			if($comid_count > 1){
	    				$ids_array = array();
	    				foreach ($comid as $value){
	    					$ids_array[] = $value['id'];
	    				}
	    				$ids_list = implode($ids_array, "','");
	    				// debugMessage($ids_list);
		    			$where_query .= " AND d.commodityid IN('".$ids_list."') ";
		    		}
				}
				if($islist){
					$bundled = false;
					$searchstring = trim($searchstring);
					$seach_array = explode(',', $searchstring);
					// debugMessage($seach_array);
					if(is_array($seach_array)){
						$ids_array = array();
						foreach ($seach_array as $string){
							$com = new Commodity();
							$comid = $com->findByName($string);
	    					$comid_count = count($comid);
							if($comid_count > 0){
			    				foreach ($comid as $value){
			    					$ids_array[] = $value['id'];
			    				}
				    		}
						}
						if(count($ids_array) > 0){
							$ids_list = implode($ids_array, "','");
			    			// debugMessage($ids_list);
				    		$where_query .= " AND d.commodityid IN('".$ids_list."') ";
						} else {
							echo "COMMODITY_LIST_INVALID";
		    				exit();
						}
					} else {
						echo "COMMODITY_LIST_INVALID";
		    			exit();
					}
				}
    		}
	    		
    		$commodityquery = true;
    		if($this->_getParam('unbundled') == '1'){
    			$bundled = false;
    		}
    		
    	}
    	# markets query
    	$marketquery = false;
   		if(!isArrayKeyAnEmptyString('marketid', $formvalues) || !isArrayKeyAnEmptyString('market', $formvalues)){
   			$market = new PriceSource();
   			if(!isEmptyString($this->_getParam('marketid'))){
	    		$market->populate($formvalues['marketid']);
	    		// debugMessage($market->toArray());
	    		if(isEmptyString($market->getID()) || !is_numeric($formvalues['marketid'])){
	    			echo "MARKET_INVALID";
	    			exit();
	    		}
	    		$makid = $formvalues['marketid'];
   			}
   			if(!isEmptyString($this->_getParam('market'))){
   				$makid = $market->findByName($formvalues['market']);
    			if(isEmptyString($makid)){
	    			echo "MARKET_INVALID";
	    			exit();
	    		}
   			}
    		$marketquery = true;
    		$where_query .= " AND d.sourceid = '".$makid."' ";
    	}
    	# district query
    	$districtquery = false;
    	if(!isArrayKeyAnEmptyString('districtid', $formvalues) || !isArrayKeyAnEmptyString('district', $formvalues)){
   			$district = new District();
   			if(!isArrayKeyAnEmptyString('districtid', $formvalues)){
	    		$district->populate($formvalues['districtid']);
	    		// debugMessage($market->toArray());
	    		if(isEmptyString($district->getID()) || !is_numeric($formvalues['districtid'])){
	    			echo "DISTRICT_INVALID";
	    			exit();
	    		}
	    		$distid = $formvalues['districtid'];
   			}
   			if(!isArrayKeyAnEmptyString('district', $formvalues)){
   				$distid = $district->findByName($formvalues['district'], 2);
    			if(isEmptyString($distid)){
	    			echo "DISTRICT_INVALID";
	    			exit();
	    		}
   			}
    		$districtquery = true;
    		if($this->_getParam('unbundled') == '1'){
    			$bundled = false;
    		}
    		$where_query .= " AND d2.districtid = '".$distid."' ";
    	}
    	# region query 
    	$regionquery = false;
    	if(!isArrayKeyAnEmptyString('regionid', $formvalues) || !isArrayKeyAnEmptyString('region', $formvalues)){
   			$region = new Region();
   			if(!isArrayKeyAnEmptyString('regionid', $formvalues)){
	    		$region->populate($formvalues['regionid']);
	    		// debugMessage(region->toArray());
	    		if(isEmptyString($region->getID()) || !is_numeric($formvalues['regionid'])){
	    			echo "REGION_INVALID";
	    			exit();
	    		}
	    		$regid = $formvalues['regionid'];
   			}
    		if(!isEmptyString($this->_getParam('region'))){
   				$regid = $region->findByName($formvalues['region'], 1);
    			if(isEmptyString($regid)){
	    			echo "REGION_INVALID";
	    			exit();
	    		}
   			}
    		$regionquery = true;
    		$bundled = true;
    		if($this->_getParam('unbundled') == '1'){
    			$bundled = false;
    		}
    		$where_query .= " AND d2.regionid = '".$regid."' ";
    	}
    	# all prices query
    	$allpricesquery = false;
    	if(!isArrayKeyAnEmptyString('range', $formvalues) && $this->_getParam('range') == 'all'){
    		$allpricesquery = true;
    	}
    	// debugMessage($where_query); // exit();
   		
    	$conn = Doctrine_Manager::connection(); 
    	$query = "SELECT 
					d2.regionid as regionid,
					d2.region as region,
					d2.districtid as districtid,
					d2.district as district,
    				d.datecollected as date, 
					REPLACE(d2.name,' Market','')as market,
					d.sourceid as marketid,
					c.name as commodity,
					d.commodityid as commodityid,
					cu.name as unit,
					d.retailprice AS retailprice, 
					d.wholesaleprice AS wholesaleprice
					FROM commoditypricedetails AS d 
					INNER JOIN commodity AS c ON (d.`commodityid` = c.id)
					LEFT JOIN commodityunit AS cu ON (c.unitid = cu.id)
					INNER JOIN (
					SELECT cp.sourceid,
					p.locationid as districtid,
					l.name as district,
					l.regionid as regionid, 
					r.name as region, 
					MAX(cp.datecollected) AS datecollected, 
					p.name FROM commoditypricedetails cp 
					INNER JOIN commoditypricesubmission AS cs1 ON (cp.`submissionid` = cs1.`id` 
					AND cs1.`status` = 'Approved') 
					INNER JOIN pricesource AS p ON (cp.sourceid = p.id AND p.`applicationtype` = 0 )
					INNER JOIN location AS l ON (p.locationid = l.id AND l.locationtype = 2)
					INNER JOIN location AS r ON (l.regionid = r.id AND r.locationtype = 1)
					WHERE cp.`pricecategoryid` = 2 GROUP BY cp.sourceid) AS d2 
					ON (d.`sourceid` = d2.sourceid AND d.`datecollected` = d2.datecollected) 
					WHERE d.`pricecategoryid` = 2 AND d.retailprice > 0 AND d.wholesaleprice > 0 AND date(d.datecollected) >= date(now()-interval 60 day) ".$where_query." ORDER BY d2.name";
    	
    	// debugMessage($query);
    	$result = $conn->fetchAll($query);
    	$pricecount = count($result);
    	// debugMessage($result); 
    	// exit();
    	if($pricecount == 0){
    		echo "RESULT_NULL";
    	} else {
    		$feed = '';
    		# format commodity output
    		if($commodityquery && !$marketquery && !$districtquery){
    			$feed = '';
    			if(!$bundled){
			    	foreach ($result as $line){
				    	$feed .= '<item>';
			    		$feed .= '<region>'.$line['region'].'</region>';
			    		$feed .= '<regionid>'.$line['regionid'].'</regionid>';
				    	$feed .= '<district>'.$line['district'].'</district>';
				    	$feed .= '<districtid>'.$line['districtid'].'</districtid>';
				    	$feed .= '<market>'.$line['market'].'</market>';
				    	$feed .= '<marketid>'.$line['marketid'].'</marketid>';
				    	$feed .= '<commodity>'.$line['commodity'].'</commodity>';
				    	$feed .= '<commodityid>'.$line['commodityid'].'</commodityid>';
				    	$feed .= '<unit>' .$line['unit'].'</unit>';
				    	$feed .= '<datecollected>'.$line['date'].'</datecollected>';
				    	$feed .= '<retailprice>'.$line['retailprice'].'</retailprice>';
					    $feed .= '<wholesaleprice>'.$line['wholesaleprice'].'</wholesaleprice>';
					    $feed .= '</item>';
			    	}
    			} else {
    				$total_rp = 0;
    				$total_wp = 0;
    				foreach ($result as $line){
    					$total_rp += $line['retailprice'];
    					$total_wp += $line['wholesaleprice'];
    				}
    				$avg_rp = $total_rp/$pricecount;
    				$avg_rp = ceil($avg_rp / 50) * 50;
    				$avg_wp = $total_wp/$pricecount;
    				$avg_wp = ceil($avg_wp / 50) * 50;
    				
    				$feed .= '<item>';
				   	$feed .= '<commodity>'.$result[0]['commodity'].'</commodity>';
				   	$feed .= '<commodityid>'.$result[0]['commodityid'].'</commodityid>';
				   	$feed .= '<unit>' .$result[0]['unit'].'</unit>';
				   	$feed .= '<datecollected>'.$result[0]['date'].'</datecollected>';
				   	$feed .= '<retailprice>'.$avg_rp.'</retailprice>';
				    $feed .= '<wholesaleprice>'.$avg_wp.'</wholesaleprice>';
				    $feed .= '<unbundled>1</unbundled>';
				    $feed .= '</item>';
    			}
    		}
    		# format market output
    		if($marketquery){
    			$feed = '';
	    		foreach ($result as $line){
			    	$feed .= '<item>';
			    	$feed .= '<region>'.$line['region'].'</region>';
			    	$feed .= '<regionid>'.$line['regionid'].'</regionid>';
			    	$feed .= '<district>'.$line['district'].'</district>';
			    	$feed .= '<districtid>'.$line['districtid'].'</districtid>';
			    	$feed .= '<market>'.$line['market'].'</market>';
			    	$feed .= '<marketid>'.$line['marketid'].'</marketid>';
			    	$feed .= '<commodity>'.$line['commodity'].'</commodity>';
			    	$feed .= '<commodityid>'.$line['commodityid'].'</commodityid>';
			    	$feed .= '<unit>' .$line['unit'].'</unit>';
			    	$feed .= '<datecollected>'.$line['date'].'</datecollected>';
			    	$feed .= '<retailprice>'.$line['retailprice'].'</retailprice>';
				    $feed .= '<wholesaleprice>'.$line['wholesaleprice'].'</wholesaleprice>';
				    $feed .= '</item>';
	    		}
    		}
    		# format region output
    		if($districtquery){
    			$feed = '';
    			if(!$bundled){
			    	foreach ($result as $line){
				    	$feed .= '<item>';
		    			$feed .= '<region>'.$line['region'].'</region>';
		    			$feed .= '<regionid>'.$line['regionid'].'</regionid>';
				    	$feed .= '<district>'.$line['district'].'</district>';
				    	$feed .= '<districtid>'.$line['districtid'].'</districtid>';
			    		$feed .= '<market>'.$line['market'].'</market>';
			    		$feed .= '<marketid>'.$line['marketid'].'</marketid>';
				    	$feed .= '<commodity>'.$line['commodity'].'</commodity>';
				    	$feed .= '<commodityid>'.$line['commodityid'].'</commodityid>';
				    	$feed .= '<unit>' .$line['unit'].'</unit>';
				    	$feed .= '<datecollected>'.$line['date'].'</datecollected>';
				    	$feed .= '<retailprice>'.$line['retailprice'].'</retailprice>';
					    $feed .= '<wholesaleprice>'.$line['wholesaleprice'].'</wholesaleprice>';
					    $feed .= '</item>';
			    	}
    			} else {
    				$total_rp = 0;
    				$total_wp = 0;
    				foreach ($result as $line){
    					$total_rp += $line['retailprice'];
    					$total_wp += $line['wholesaleprice'];
    				}
    				$avg_rp = $total_rp/$pricecount;
    				$avg_rp = ceil($avg_rp / 50) * 50;
    				$avg_wp = $total_wp/$pricecount;
    				$avg_wp = ceil($avg_wp / 50) * 50;
    					
    				$feed .= '<item>';
			    	$feed .= '<region>'.$result[0]['region'].'</region>';
			    	$feed .= '<regionid>'.$result[0]['regionid'].'</regionid>';
				    $feed .= '<district>'.$result[0]['district'].'</district>';
				    $feed .= '<districtid>'.$result[0]['districtid'].'</districtid>';
				    $feed .= '<commodity>'.$result[0]['commodity'].'</commodity>';
				    $feed .= '<commodityid>'.$result[0]['commodityid'].'</commodityid>';
				    $feed .= '<unit>' .$result[0]['unit'].'</unit>';
				    $feed .= '<datecollected>'.$result[0]['date'].'</datecollected>';
				    $feed .= '<retailprice>'.$avg_rp.'</retailprice>';
					$feed .= '<wholesaleprice>'.$avg_wp.'</wholesaleprice>';
					$feed .= '<unbundled>1</unbundled>';
					$feed .= '</item>';
    			}
    		}
    		# format region output
    		if($regionquery){
    			$feed = '';
    			if(!$bundled){
			    	foreach ($result as $line){
					    $feed .= '<item>';
			    		$feed .= '<region>'.$line['region'].'</region>';
			    		$feed .= '<regionid>'.$line['regionid'].'</regionid>';
					    $feed .= '<district>'.$line['district'].'</district>';
					    $feed .= '<districtid>'.$line['districtid'].'</districtid>';
				    	$feed .= '<market>'.$line['market'].'</market>';
				    	$feed .= '<marketid>'.$line['marketid'].'</marketid>';
					    $feed .= '<commodity>'.$line['commodity'].'</commodity>';
					    $feed .= '<commodityid>'.$line['commodityid'].'</commodityid>';
					    $feed .= '<unit>' .$line['unit'].'</unit>';
					    $feed .= '<datecollected>'.$line['date'].'</datecollected>';
					    $feed .= '<retailprice>'.$line['retailprice'].'</retailprice>';
						$feed .= '<wholesaleprice>'.$line['wholesaleprice'].'</wholesaleprice>';
						$feed .= '</item>';
			    	}
    			} else {
    				$total_rp = 0;
    				$total_wp = 0;
    				foreach ($result as $line){
    					$total_rp += $line['retailprice'];
    					$total_wp += $line['wholesaleprice'];
    				}
    				$avg_rp = $total_rp/$pricecount;
    				$avg_rp = ceil($avg_rp / 50) * 50;
    				$avg_wp = $total_wp/$pricecount;
    				$avg_wp = ceil($avg_wp / 50) * 50;
    				
    				$feed .= '<item>';
			    	$feed .= '<region>'.$result[0]['region'].'</region>';
			    	$feed .= '<regionid>'.$result[0]['regionid'].'</regionid>';
				    $feed .= '<commodity>'.$result[0]['commodity'].'</commodity>';
				    $feed .= '<commodityid>'.$result[0]['commodityid'].'</commodityid>';
				    $feed .= '<unit>' .$result[0]['unit'].'</unit>';
				    $feed .= '<datecollected>'.$result[0]['date'].'</datecollected>';
				    $feed .= '<retailprice>'.$avg_rp.'</retailprice>';
					$feed .= '<wholesaleprice>'.$avg_wp.'</wholesaleprice>';
					$feed .= '<unbundled>1</unbundled>';
					$feed .= '</item>';
    			}
    		}
    		# format all prices output
    		if($allpricesquery){
    			$feed = '';
    			foreach ($result as $line){
			    	$feed .= '<item>';
			    	$feed .= '<region>'.$line['region'].'</region>';
			    	$feed .= '<regionid>'.$line['regionid'].'</regionid>';
			    	$feed .= '<district>'.$line['district'].'</district>';
			    	$feed .= '<districtid>'.$line['districtid'].'</districtid>';
		    		$feed .= '<market>'.$line['market'].'</market>';
		    		$feed .= '<marketid>'.$line['marketid'].'</marketid>';
			    	$feed .= '<commodity>'.$line['commodity'].'</commodity>';
			    	$feed .= '<commodityid>'.$line['commodityid'].'</commodityid>';
			    	$feed .= '<unit>' .$line['unit'].'</unit>';
			    	$feed .= '<datecollected>'.$line['date'].'</datecollected>';
			    	$feed .= '<retailprice>'.$line['retailprice'].'</retailprice>';
				    $feed .= '<wholesaleprice>'.$line['wholesaleprice'].'</wholesaleprice>';
				    $feed .= '</item>';
	    		}
    		}
    		
    		# output the xml returned
    		if(isEmptyString($feed)){
    			echo "EXCEPTION_ERROR";
    		} else {
    			echo '<?xml version="1.0" encoding="UTF-8"?><items>'.$feed.'</items>';
    		}
	    }
    }
    
	function commodityAction() {
    	// disable rendering of the view and layout 
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    $conn = Doctrine_Manager::connection(); 
    	$session = SessionWrapper::getInstance();
    	$formvalues = $this->_getAllParams();
    	// debugMessage($formvalues);
    	// debugMessage($this->_getAllParams());
    	
    	$feed = '';
    	$hasall = true;
    	$issearch = false;
    	$iscount = false;
    	$ispaged = false;
    	$where_query = " ";
    	$type_query = " AND cp.pricecategoryid = '2' ";
    	$group_query = " GROUP BY c.id ";
    	$limit_query = "";
    	$select_query = " c.id, c.name as `Commodity`, cat.name as Category, c.description as Description, 
    		u.`name` as `Unit`";
    	
    	if(isArrayKeyAnEmptyString('filter', $formvalues)){
    		echo "NO_FILTER_SPECIFIED";
    		exit();
    	}
    	
    	$iscategory = false;
    	# fetch commodities filtered by categoryid
    	if($formvalues['filter'] == 'categoryid'){
    		$catid = '';
    		if(isEmptyString($this->_getParam('catid'))){
    			echo "CATEGORY_NULL";
	    		exit();
    		}
    		if(!is_numeric($this->_getParam('catid'))){
    			echo "CATEGORY_INVALID";
	    		exit();
    		} 
    		if(!isEmptyString($this->_getParam('catid'))){
    			$category = new CommodityCategory();
	    		$category->populate($formvalues['catid']);
	    		// debugMessage($category->toArray());
	    		if(isEmptyString($category->getID())){
	    			echo "CATEGORY_INVALID";
	    			exit();
	    		}
	    		$catid = $formvalues['catid'];
	    		$iscategory = true;
    			$hasall = false;
    			$where_query .= " AND c.categoryid = '".$catid."' ";
   			}
    		if(isEmptyString($catid)){
	    		echo "CATEGORY_NULL";
		    	exit();
	    	}
    	}
    	# fetch commodities filtered by category name
   		if($formvalues['filter'] == 'category'){
    		$catid = '';
    		if(isEmptyString($this->_getParam('catname'))){
    			echo "CATEGORY_NULL";
	    		exit();
    		}
    		if(is_numeric($this->_getParam('catname'))){
    			echo "CATEGORY_INVALID";
	    		exit();
    		} 
    		if(!isEmptyString($this->_getParam('catname'))){
    			$category = new CommodityCategory();
	    		$catid = $category->findByName($formvalues['catname']);
	    		if(isEmptyString($catid)){
	    			echo "CATEGORY_INVALID";
	    			exit();
	    		}
	    		$iscategory = true;
    			$hasall = false;
    			$where_query .= " AND c.categoryid = '".$catid."' ";
   			}
	   		if(isEmptyString($catid)){
	    		echo "CATEGORY_NULL";
		    	exit();
	    	}
    	}
    		
    	# searching for particular commodity
    	if($formvalues['filter'] == 'search'){
    		// check that search parameters have been specified
    		if(isEmptyString($this->_getParam('comid')) && isEmptyString($this->_getParam('comname'))){
    			echo "NULL_SEARCH_PARAMETER";
	    		exit();
    		}
    		$com = new Commodity();
    		// commodityid specified
    		if(!isEmptyString($this->_getParam('comid'))){
	    		$com->populate($formvalues['comid']);
	    		// debugMessage($com->toArray());
	    		if(isEmptyString($com->getID()) || !is_numeric($formvalues['comid'])){
	    			echo "COMMODITY_INVALID";
	    			exit();
	    		}
	    		$comid = $com->getID();
	    		$where_query .= " AND c.id = '".$comid."' ";
    		}
    		/*if(!isEmptyString($this->_getParam('comname')) ){
	    		// debugMessage($com->toArray());
	    		if(isEmptyString($this->_getParam('comname'))){
	    			echo "CATEGORY_NULL";
		    		exit();
	    		}
	    		$where_query .= " AND c.name LIKE '%".$this->_getParam('comname')."%'  ";
    		}*/
    		// commodty name specified
    		if(!isEmptyString($this->_getParam('comname'))){
    			$searchstring = $formvalues['comname'];
    			$islist = false;
	    		if(strpos($searchstring, ',') !== false) {
					$islist = true;
				}
				if(!$islist){
	    			$comid = $com->findByName($searchstring);
	    			$comid_count = count($comid);
	    			if($comid_count == 0){
		    			echo "COMMODITY_INVALID";
		    			exit();
		    		}
		    		if($comid_count == 1){
		    			$where_query .= " AND c.id = '".$comid[0]['id']."' ";
		    		}
	    			if($comid_count > 1){
	    				$ids_array = array();
	    				foreach ($comid as $value){
	    					$ids_array[] = $value['id'];
	    				}
	    				$ids_list = implode($ids_array, "','");
	    				// debugMessage($ids_list);
		    			$where_query .= " AND c.id IN('".$ids_list."') ";
		    		}
				}
				if($islist){
					$bundled = false;
					$searchstring = trim($searchstring);
					$seach_array = explode(',', $searchstring);
					// debugMessage($seach_array);
					if(is_array($seach_array)){
						$ids_array = array();
						foreach ($seach_array as $string){
							$com = new Commodity();
							$comid = $com->findByName($string);
	    					$comid_count = count($comid);
							if($comid_count > 0){
			    				foreach ($comid as $value){
			    					$ids_array[] = $value['id'];
			    				}
				    		}
						}
						if(count($ids_array) > 0){
							$ids_list = implode($ids_array, "','");
			    			// debugMessage($ids_list);
				    		$where_query .= " AND c.id IN('".$ids_list."') ";
						} else {
							echo "COMMODITY_LIST_INVALID";
		    				exit();
						}
					} else {
						echo "COMMODITY_LIST_INVALID";
		    			exit();
					}
				}
    		}
    	}
    	
    	// when fetching total results
    	if(!isEmptyString($this->_getParam('fetch')) && $this->_getParam('fetch') == 'total'){
    		$select_query = " count(c.id) as records ";
    		$group_query = "";
    		$iscount = true;
    	}
    	// debugMessage($select_query);
    	// exit();
    	// when fetching limited results via pagination 
    	if(!isEmptyString($this->_getParam('paged')) && $this->_getParam('paged') == 'Y'){
    		$ispaged = true;
    		$hasall = false;
    		$start = $this->_getParam('start');
    		$limit = $this->_getParam('limit');
    		if(isEmptyString($start)){
    			echo "RANGE_START_NULL";
	    		exit();
    		}
    		if(!is_numeric($limit)){
    			echo "INVALID_RANGE_START";
	    		exit();
    		}
    		if(isEmptyString($start)){
    			echo "RANGE_LIMIT_NULL";
	    		exit();
    		}
    		if(!is_numeric($limit)){
    			echo "INVALID_RANGE_LIMIT";
	    		exit();
    		}
    		$limit_query = " limit ".$start.",".$limit." ";
    	}
    	
    	// if searching fuel
    	if(!isEmptyString($this->_getParam('pricetype')) && $this->_getParam('pricetype') == 'fuel'){
    		$type_query = " AND cp.pricecategoryid = '4' ";
    	}
    	// action query
    	
    	$com_query = "SELECT ".$select_query." FROM commodity c 
    	INNER JOIN commoditycategory cat ON c.categoryid = cat.id 
    	LEFT JOIN commodity p ON c.parentid = p.id LEFT JOIN 
    	commoditypricecategory as cp ON (c.id = cp.commodityid) 
    	LEFT JOIN commodityunit as u ON(c.unitid = u.id) 
    	WHERE c.name <> '' ".$type_query.$where_query."  
    	".$group_query." ORDER BY c.name ASC ".$limit_query;
    	// debugMessage($com_query);
    	
    	$result = $conn->fetchAll($com_query);
    	$comcount = count($result);
    	// debugMessage($result); // exit();
    	
    	if($comcount == 0){
    		echo "RESULT_NULL";
    		exit();
    	} else {
    		if($iscount){
    			$feed .= '<item>';
	    		$feed .= '<total>'.$result[0]['records'].'</total>';
			    $feed .= '</item>';
    		}
    		if(!$iscount){
	    		foreach ($result as $line){
	    			$com = new Commodity();
	    			$com->populate($line['id']);
	    			$prices = $com->getCurrentAveragePrice();
	    			// debugMessage($prices);
			    	$feed .= '<item>';
		    		$feed .= '<id>'.$line['id'].'</id>';
		    		$feed .= '<name>'.$line['Commodity'].'</name>';
			    	$feed .= '<cat>'.$line['Category'].'</cat>';
			    	$feed .= '<unit>'.$line['Unit'].'</unit>';
			    	$feed .= '<avg_wholesaleprice>'.$prices['wp'].'</avg_wholesaleprice>';
			    	$feed .= '<avg_retailprice>'.$prices['rp'].'</avg_retailprice>';
			    	$feed .= '<pricedate>'.$prices['datecollected'].'</pricedate>';
				    $feed .= '</item>';
		    	}
    		}
    	}
    	
    	# output the xml returned
    	if(isEmptyString($feed)){
    		echo "EXCEPTION_ERROR";
    		exit();
    	} else {
    		echo '<?xml version="1.0" encoding="UTF-8"?><items>'.$feed.'</items>';
    	}
    }
    
    function locationsAction() {
    	// disable rendering of the view and layout 
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    $conn = Doctrine_Manager::connection(); 
    	$session = SessionWrapper::getInstance();
    	$formvalues = $this->_getAllParams();
    	// debugMessage($formvalues);
    	// debugMessage($this->_getAllParams());
    	
   	 	$feed = '';
    	$hasall = true;
    	$issearch = false;
    	$iscount = false;
    	$ispaged = false;
    	$where_query = " ";
    	$type_query = "  ";
    	$group_query = " GROUP BY l.id ";
    	$limit_query = "";
    	$select_query = " l.id, l.name as `Location`, l.locationtype as `Type`,
    		CASE `locationtype` 
    			WHEN '1' THEN 'Region' 
    			WHEN '2' THEN 'District' 
    			WHEN '3' THEN 'County' 
    			WHEN '4' THEN 'SubCounty' 
    			WHEN '5' THEN 'Parish' 
    			WHEN '6' THEN 'Village' 
    		END AS `TypeName` ";
    	$join_query = "";
    	
    	if(isArrayKeyAnEmptyString('filter', $formvalues)){
    		echo "NO_FILTER_SPECIFIED";
    		exit();
    	}
    	
    	$isregion = false; $isdistrict = false; $iscounty = false; $issubcounty = false; $isparish = false; $isvillage = false;
    	$loc_type = '';
    	# fetch commodities filtered by categoryid
    	if($formvalues['filter'] == 'type'){
    		switch ($this->_getParam('category')) {
    			case 'region':
    			case 1:
    				$where_query .= " AND l.locationtype = 1 ";
	    			$type_query .= " ";
	    			$isregion = true;
    				break;
    			case 'district':
    			case 2:
    				$where_query .= " AND l.locationtype = 2 ";
	    			$type_query .= " ";
	    			$isdistrict = true;
			    	if(!isArrayKeyAnEmptyString('regionid', $formvalues) || !isArrayKeyAnEmptyString('region', $formvalues)){
			   			$region = new Region();
			   			if(!isArrayKeyAnEmptyString('regionid', $formvalues)){
				    		$region->populate($formvalues['regionid']);
				    		// debugMessage(region->toArray());
				    		if(isEmptyString($region->getID()) || !is_numeric($formvalues['regionid'])){
				    			echo "REGION_INVALID";
				    			exit();
				    		}
				    		$regid = $formvalues['regionid'];
			   			}
			    		if(!isEmptyString($this->_getParam('region'))){
			   				$regid = $region->findByName($formvalues['region'], 1);
			    			if(isEmptyString($regid)){
				    			echo "REGION_INVALID";
				    			exit();
				    		}
			   			}
			    		$where_query .= " AND l.regionid = '".$regid."' ";
			    	}
    				break;
    			case 'county':
    			case 3:
    				$where_query .= " AND l.locationtype = 3 ";
	    			$type_query .= " ";
	    			$iscounty = true;
			    	if(!isArrayKeyAnEmptyString('districtid', $formvalues) || !isArrayKeyAnEmptyString('district', $formvalues)){
			   			$district = new District();
			   			if(!isArrayKeyAnEmptyString('districtid', $formvalues)){
				    		$district->populate($formvalues['districtid']);
				    		// debugMessage($market->toArray());
				    		if(isEmptyString($district->getID()) || !is_numeric($formvalues['districtid'])){
				    			echo "DISTRICT_INVALID";
				    			exit();
				    		}
				    		$distid = $formvalues['districtid'];
			   			}
			   			if(!isArrayKeyAnEmptyString('district', $formvalues)){
			   				$distid = $district->findByName($formvalues['district'], 2);
			    			if(isEmptyString($distid)){
				    			echo "DISTRICT_INVALID";
				    			exit();
				    		}
			   			}
			    		$where_query .= " AND l.districtid = '".$distid."' ";
			    	}
    				break;
    			case 'subcountry':
    			case 2:
    				$where_query .= " AND l.locationtype = 4 ";
	    			$type_query .= " ";
	    			$issubcounty = true;
    				break;
    			case 'parish':
    			case 2:
    				$where_query .= " AND l.locationtype = 5 ";
	    			$type_query .= " ";
	    			$isparish = true;
    				break;
    			case 'village':
    			case 2:
    				$where_query .= " AND l.locationtype = 6 ";
	    			$type_query .= " ";
	    			$isvillage = true;
    				break;
    			default:
    				break;
    		}
    	}
    	
    	// when fetching total results
    	if(!isEmptyString($this->_getParam('fetch')) && $this->_getParam('fetch') == 'total'){
    		$select_query = " count(l.id) as records ";
    		$group_query = "";
    		$iscount = true;
    	}
    	
    	// when fetching limited results via pagination 
    	if(!isEmptyString($this->_getParam('paged')) && $this->_getParam('paged') == 'Y'){
    		$ispaged = true;
    		$hasall = false;
    		$start = $this->_getParam('start');
    		$limit = $this->_getParam('limit');
    		if(isEmptyString($start)){
    			echo "RANGE_START_NULL";
	    		exit();
    		}
    		if(!is_numeric($limit)){
    			echo "INVALID_RANGE_START";
	    		exit();
    		}
    		if(isEmptyString($start)){
    			echo "RANGE_LIMIT_NULL";
	    		exit();
    		}
    		if(!is_numeric($limit)){
    			echo "INVALID_RANGE_LIMIT";
	    		exit();
    		}
    		$limit_query = " limit ".$start.",".$limit." ";
    	}
    	
    	// 
    	$com_query = "SELECT ".$select_query." FROM location l WHERE l.name <> '' ".$type_query.$where_query."  
    	".$group_query." ORDER BY l.name ASC ".$limit_query;
    	debugMessage($com_query);
    	
    	$result = $conn->fetchAll($com_query);
    	$comcount = count($result);
    	// debugMessage($result); // exit();
    	
    	if($comcount == 0){
    		echo "RESULT_NULL";
    		exit();
    	} else {
    		if($iscount){
    			$feed .= '<item>';
	    		$feed .= '<total>'.$result[0]['records'].'</total>';
			    $feed .= '</item>';
    		}
    		if(!$iscount){
	    		foreach ($result as $line){
			    	$feed .= '<item>';
		    		$feed .= '<id>'.$line['id'].'</id>';
		    		$feed .= '<name>'.$line['Location'].'</name>';
		    		$feed .= '<type>'.$line['Type'].'</type>';
			    	$feed .= '<typename>'.$line['TypeName'].'</typename>';
				    $feed .= '</item>';
		    	}
    		}
    	}
    	
    	# output the xml returned
    	if(isEmptyString($feed)){
    		echo "EXCEPTION_ERROR";
    		exit();
    	} else {
    		echo '<?xml version="1.0" encoding="UTF-8"?><items>'.$feed.'</items>';
    	}
    }
    
    function marketsAction() {
    	// disable rendering of the view and layout 
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(TRUE);
	    $conn = Doctrine_Manager::connection(); 
    	$session = SessionWrapper::getInstance();
    	$formvalues = $this->_getAllParams();
    	// debugMessage($formvalues);
    	// debugMessage($this->_getAllParams());
    	
   	 	$feed = '';
    	$hasall = true;
    	$issearch = false;
    	$iscount = false;
    	$ispaged = false;
    	$where_query = " ";
    	$type_query = " AND pc.pricecategoryid = '2' ";
    	$group_query = " GROUP BY p.id ";
    	$limit_query = "";
    	$select_query = " p.id, p.name as market, p.locationid as districtid, l.regionid as regionid, l.name as district, lr.name as region ";
    	$join_query = " INNER JOIN location AS l ON (p.locationid = l.id AND l.locationtype = 2) 
    					INNER JOIN pricesourcecategory AS pc ON (p.id = pc.pricesourceid) 
    					INNER JOIN location AS lr ON (l.regionid = lr.id) ";
    	
    	if(isArrayKeyAnEmptyString('filter', $formvalues)){
    		echo "NO_FILTER_SPECIFIED";
    		exit();
    	}
    	
    	# fetch commodities filtered by categoryid
    	if($this->_getParam('filter') == 'all'){
	    	$districtquery = false;
	    	if(!isArrayKeyAnEmptyString('districtid', $formvalues) || !isArrayKeyAnEmptyString('district', $formvalues)){
	   			$district = new District();
	   			if(!isArrayKeyAnEmptyString('districtid', $formvalues)){
		    		$district->populate($formvalues['districtid']);
		    		// debugMessage($market->toArray());
		    		if(isEmptyString($district->getID()) || !is_numeric($formvalues['districtid'])){
		    			echo "DISTRICT_INVALID";
		    			exit();
		    		}
		    		$distid = $formvalues['districtid'];
	   			}
	   			if(!isArrayKeyAnEmptyString('district', $formvalues)){
	   				$distid = $district->findByName($formvalues['district'], 2);
	    			if(isEmptyString($distid)){
		    			echo "DISTRICT_INVALID";
		    			exit();
		    		}
	   			}
	    		$districtquery = true;
	    		$where_query .= " AND p.locationid = '".$distid."' ";
	    	}
	    	
	    	$regionquery = false;
	    	if(!isArrayKeyAnEmptyString('regionid', $formvalues) || !isArrayKeyAnEmptyString('region', $formvalues)){
	   			$region = new Region();
	   			if(!isArrayKeyAnEmptyString('regionid', $formvalues)){
		    		$region->populate($formvalues['regionid']);
		    		// debugMessage(region->toArray());
		    		if(isEmptyString($region->getID()) || !is_numeric($formvalues['regionid'])){
		    			echo "REGION_INVALID";
		    			exit();
		    		}
		    		$regid = $formvalues['regionid'];
	   			}
	    		if(!isEmptyString($this->_getParam('region'))){
	   				$regid = $region->findByName($formvalues['region'], 1);
	    			if(isEmptyString($regid)){
		    			echo "REGION_INVALID";
		    			exit();
		    		}
	   			}
	    		$regionquery = true;
	    		$where_query .= " AND l.regionid = '".$regid."' ";
	    	}
    	}
    	
    	# searching for particular commodity
    	if($formvalues['filter'] == 'search'){
    		// check that search parameters have been specified
    		if(isEmptyString($this->_getParam('marketid')) && isEmptyString($this->_getParam('market'))){
    			echo "NULL_SEARCH_PARAMETER";
	    		exit();
    		}
    		$source = new PriceSource();
    		// pricesourceid specified
    		if(!isEmptyString($this->_getParam('marketid'))){
	    		$source->populate($formvalues['marketid']);
	    		// debugMessage($com->toArray());
	    		if(isEmptyString($source->getID()) || !is_numeric($formvalues['marketid'])){
	    			echo "MARKET_INVALID";
	    			exit();
	    		}
	    		$sourceid = $source->getID();
	    		$where_query .= " AND p.id = '".$sourceid."' ";
    		}
    		// market name specified
    		if(!isEmptyString($this->_getParam('market'))){
    			$searchstring = $formvalues['market'];
    			$islist = false;
	    		if(strpos($searchstring, ',') !== false) {
					$islist = true;
				}
				if(!$islist){
	    			$sourceid = $source->findByName($searchstring);
	    			$sourceid_count = count($sourceid);
	    			if($sourceid_count == 0){
		    			echo "MARKET_INVALID";
		    			exit();
		    		}
		    		if($sourceid_count == 1){
		    			$where_query .= " AND c.id = '".$sourceid[0]['id']."' ";
		    		}
	    			if($sourceid_count > 1){
	    				$ids_array = array();
	    				foreach ($sourceid as $value){
	    					$ids_array[] = $value['id'];
	    				}
	    				$ids_list = implode($ids_array, "','");
	    				// debugMessage($ids_list);
		    			$where_query .= " AND p.id IN('".$ids_list."') ";
		    		}
				}
				if($islist){
					$bundled = false;
					$searchstring = trim($searchstring);
					$seach_array = explode(',', $searchstring);
					// debugMessage($seach_array);
					if(is_array($seach_array)){
						$ids_array = array();
						foreach ($seach_array as $string){
							$source = new PriceSource();
							$sourceid = $source->findByName($string);
	    					$sourceid_count = count($sourceid);
							if($sourceid_count > 0){
			    				foreach ($sourceid as $value){
			    					$ids_array[] = $value['id'];
			    				}
				    		}
						}
						if(count($ids_array) > 0){
							$ids_list = implode($ids_array, "','");
			    			// debugMessage($ids_list);
				    		$where_query .= " AND c.id IN('".$ids_list."') ";
						} else {
							echo "MARKET_LIST_INVALID";
		    				exit();
						}
					} else {
						echo "MARKET_LIST_INVALID";
		    			exit();
					}
				}
    		}
    	}
    	
    	// when fetching total results
    	if(!isEmptyString($this->_getParam('fetch')) && $this->_getParam('fetch') == 'total'){
    		$select_query = " count(l.id) as records ";
    		$group_query = "";
    		$iscount = true;
    	}
    	
    	// when fetching limited results via pagination 
    	if(!isEmptyString($this->_getParam('paged')) && $this->_getParam('paged') == 'Y'){
    		$ispaged = true;
    		$hasall = false;
    		$start = $this->_getParam('start');
    		$limit = $this->_getParam('limit');
    		if(isEmptyString($start)){
    			echo "RANGE_START_NULL";
	    		exit();
    		}
    		if(!is_numeric($limit)){
    			echo "INVALID_RANGE_START";
	    		exit();
    		}
    		if(isEmptyString($start)){
    			echo "RANGE_LIMIT_NULL";
	    		exit();
    		}
    		if(!is_numeric($limit)){
    			echo "INVALID_RANGE_LIMIT";
	    		exit();
    		}
    		$limit_query = " limit ".$start.",".$limit." ";
    	}
    	
    	// 
    	$com_query = "SELECT ".$select_query." FROM pricesource p ".$join_query." WHERE p.name <> '' ".$type_query.$where_query."  
    	".$group_query." ORDER BY p.name ASC ".$limit_query;
    	// debugMessage($com_query);
    	
    	$result = $conn->fetchAll($com_query);
    	$comcount = count($result);
    	// debugMessage($result); exit();
    	
    	if($comcount == 0){
    		echo "RESULT_NULL";
    		exit();
    	} else {
    		if($iscount){
    			$feed .= '<item>';
	    		$feed .= '<total>'.$result[0]['records'].'</total>';
			    $feed .= '</item>';
    		}
    		if(!$iscount){
	    		foreach ($result as $line){
			    	$feed .= '<item>';
		    		$feed .= '<id>'.$line['id'].'</id>';
		    		$feed .= '<name>'.str_replace('Market', '', $line['market']).'</name>';
		    		$feed .= '<districtid>'.$line['districtid'].'</districtid>';
		    		$feed .= '<district>'.$line['district'].'</district>';
		    		$feed .= '<regionid>'.$line['regionid'].'</regionid>';
		    		$feed .= '<region>'.$line['region'].'</region>';
				    $feed .= '</item>';
		    	}
    		}
    	}
    	
    	# output the xml returned
    	if(isEmptyString($feed)){
    		echo "EXCEPTION_ERROR";
    		exit();
    	} else {
    		echo '<?xml version="1.0" encoding="UTF-8"?><items>'.$feed.'</items>';
    	}
    }
}

