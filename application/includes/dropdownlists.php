<?php

	# This class require_onces functions to access and use the different drop down lists within
	# this application

	/**
	 * function to return the results of an options query as array. This function assumes that
	 * the query returns two columns optionvalue and optiontext which correspond to the corresponding key
	 * and values respectively. 
	 * 
	 * The selection of the names is to avoid name collisions with database reserved words
	 * 
	 * @param String $query The database query
	 * 
	 * @return Array of values for the query 
	 */
	function getOptionValuesFromDatabaseQuery($query) {
		$conn = Doctrine_Manager::connection(); 
		$result = $conn->fetchAll($query);
		$valuesarray = array();
		foreach ($result as $value) {
			$valuesarray[$value['optionvalue']]	= htmlentities($value['optiontext']);
		}
		return decodeHtmlEntitiesInArray($valuesarray);
	}
	/**
	 * Get the parent commodities for the current commodity  
	 * 
	 * @param Integer $commodityid The id of the current commodity
	 * 
	 * @return Array  
	 */
	function getParentCommodityValues($commodityid) {
		$filter = "";
		if (!isEmptyString($commodityid)) {
			$filter = " WHERE id <> '".$commodityid."' ";  
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM commodity ".$filter." ORDER BY optiontext"; 
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the parent commodity categories for the current commodity category 
	 * 
	 * @param Integer $commoditycategoryid The id of the current commodity category 
	 * 
	 * @return Array  
	 */
	function getParentCommodityCategoryValues($commoditycategoryid) {
		$filter = "";
		if (!isEmptyString($commoditycategoryid)) {
			$filter = " WHERE id <> '".$commoditycategoryid."' ";  
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM commoditycategory ".$filter." ORDER BY optiontext"; 
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the parent commodity categories for the current business directory category 
	 * 
	 * @param Integer $commoditycategoryid The id of the current business directory category 
	 * 
	 * @return Array  
	 */
	function getParentBusinessDirectoryCategoryValues($businessdirectorycategoryid) {
		$filter = "";
		if (!isEmptyString($businessdirectorycategoryid)) {
			$filter = " WHERE id <> '".$businessdirectorycategoryid."' ";  
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM businessdirectorycategory ".$filter." ORDER BY optiontext"; 
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the districts in the specified region 
	 * 
	 * @param Integer $regionid The id of the region 
	 * 
	 * @return Array  
	 */
	function getDistrictsInRegion($regionid) {
		if (isEmptyString($regionid)) {
			return array(); 
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM location WHERE regionid = '".$regionid."' AND locationtype = 2 ORDER BY optiontext"; 
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the Counties in the specified region 
	 * 
	 * @param Integer $districtid The id of the district 
	 * 
	 * @return Array  
	 */
	function getCountiesInDistrict($districtid) {
		if (isEmptyString($districtid)) {
			return array(); 
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM location WHERE districtid = '".$districtid."' AND locationtype = 3 ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the Sub-Counties in the specified County 
	 * 
	 * @param Integer $countyid The id of the county 
	 * 
	 * @return Array  
	 */
	function getSubcountiesInCounty($countyid) {
		if (isEmptyString($countyid)) {
			return array(); 
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM location WHERE countyid = '".$countyid."' AND locationtype = 4 ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the Parishes in the specified Sub-County 
	 * 
	 * @param Integer $subcountyid The id of the sub-county 
	 * 
	 * @return Array  
	 */
	function getParishesInSubCounty($subcountyid) {
		if (isEmptyString($subcountyid)) {
			return array(); 
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM location WHERE subcountyid = '".$subcountyid."' AND locationtype = 5 ORDER BY optiontext";		
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the Villages in the specified Parish
	 * 
	 * @param Integer $parishid The id of the parish
	 * 
	 * @return Array  
	 */
	function getVillagesInParishes($parishid) {
		if (isEmptyString($parishid)) {
			return array(); 
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM location WHERE parishid = '".$parishid."' AND locationtype = 6 ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the sub-counties in the specified district
	 * 
	 * @param Integer $districtid - the id of the district
	 * 
	 * @return Array  
	 */
	function getSubcountiesInDistrict($districtid) {
		if (isEmptyString($districtid)) {
			return array(); 
		}
		$query = "SELECT id as optionvalue, name as optiontext FROM location WHERE districtid = '".$districtid."' AND locationtype = 4 ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the parishes in the specified district
	 *
	 * @param Integer $districtid - the id of the district
	 *
	 * @return Array
	 */
	function getParishesInDistrict($districtid) {
	    if (isEmptyString($districtid)) {
	        return array();
	    }
	    $query = "SELECT id as optionvalue, name as optiontext FROM location WHERE districtid = '".$districtid."' AND locationtype = 5 ORDER BY optiontext";
	    return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the Price Sources in the specified District
	 * 
	 * @param Integer $parishid The id of the parish
	 * 
	 * @return Array  
	 */
	function getPricesSourcesInDistrict($locationid,$categoryid) {
		if (isEmptyString($locationid) || isEmptyString($categoryid)) {
			return array($categoryid); 
		}		
		$query = "SELECT p.id AS optionvalue , p.name AS optiontext FROM pricesource AS p Inner Join pricesourcecategory AS pc ON (p.id = pc.pricesourceid AND pc.pricecategoryid = '".$categoryid."') WHERE locationid='".$locationid."' ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($query);
	}
	/**
	 * Get the prices sources for a user 
	 * 
	 * @param Integer $userid The id of the user
	 * 
	 * @return Array  
	 */
	function getMarketsForOrganization($organisationid, $user) {
		$sql = "";
		if(!isEmptyString($user)){
			$sql = " AND u.locationid = p.locationid AND u.id = ".$user;
		}
		$query = "SELECT p.id AS optionvalue, p.name AS optiontext FROM pricesource AS p Inner Join organisationdistrict AS od ON (p.locationid = od.districtid) Inner Join pricesourcecategory AS pc ON (p.id = pc.pricesourceid) Inner JOIN useraccount as u ON (od.organisationid = u.organisationid ) WHERE od.organisationid = '".$organisationid."' AND pc.pricecategoryid = 2 AND p.applicationtype = 1 ".$sql." GROUP BY p.id ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($query);
	}

	# function to generate months
	function getAllMonths() {
		$months = array(
		"January" => "January",		
		"February" => "February",
		"March" => "March",
		"April" => "April",
		"May" => "May",		
		"June" => "June",
		"July" => "July",
		"August" => "August",
		"September" => "September",		
		"October" => "October",
		"November" => "November",
		"December" => "December"	
		);
		return $months;
	}
	
	# function to generate months
	function getAllMonthsAsNumbers() {
		$months = array(
		"01" => "January",		
		"02" => "February",
		"03" => "March",
		"04" => "April",
		"05" => "May",		
		"06" => "June",
		"07" => "July",
		"08" => "August",
		"09" => "September",		
		"10" => "October",
		"11" => "November",
		"12" => "December"	
		);
		return $months;
	}
	
	function getMonthName($number) {
		$months = getAllMonthsAsNumbers();
		return $months[$number];
	}
	
	# function to generate years
	function getAllYears() {				
		$aconfig = Zend_Registry::get("config"); 
		$years = array(); 
		$start_year = date("Y") - $aconfig->dateandtime->mindate;
		
		$end_year = date("Y") + $aconfig->dateandtime->maxdate;
		for($i = $start_year; $i <= $end_year; $i++) {
			$years[$i] = $i; 
		}		
		//return the years in descending order from the last year to the first and add true to maintain the array keys
		return array_reverse($years, true);
	}
	
	# get the first day of a month
	function getFirstDayOfMonth($month,$year) {
		return date("M j, Y", mktime(0,0,0, $month,1,$year));
	}
	# get the last day of a month
	function getLastDayOfMonth($month,$year) {
		return date("M j, Y", mktime(0,0,0, $month+1,0,$year));
	}
	# get the first day of current month
	function getFirstDayOfNextMonth($month,$year) {
		return date("Y-m-d", mktime(0,0,0, $month,2,$year));
	}
	# get the last day of the next month
	function getLastDayOfNextMonth($month,$year) {
		return date("Y-m-d", mktime(0,0,0, $month+2,0,$year));
	}
	# get the first day of last month
	function getFirstDayOfLastMonth($month,$year) {
		return date("Y-m-d", mktime(0,0,0, $month,-1,$year));
	}
	# get the last day of the last month
	function getLastDayOfLastMonth($month,$year) {
		return date("Y-m-d", mktime(0,0,0, $month-1,0,$year));
	}
	# get the first day of the current year and month
	function getFirstDayOfCurrentMonth(){
		return date("Y-m-d", mktime (0,0,0, date("n"),1, date("Y")));
	}
	# get the last day of the last month
	function getLastDayOfCurrentMonth() {
		return date("Y-m-d", mktime(0,0,0, date("n")+1,0, date("Y")));
	}
	/**
	 * Generate an array for the first day of the months for which prices are available
	 * 
	 * @return Array 
	 */
	function getMonthsForStartFilter(){
		$config = Zend_Registry::get('config'); 
		$month = strtotime($config->commodityprice->firstmarketpricedate);
		$end = strtotime(getLastDayOfCurrentMonth());
		$startarray = array();
	
		while($month <= $end){		
			$curyear = date('Y', $month);
			$curmonth = date('n', $month);
			$firstday_curmonth = getFirstDayOfMonth($curmonth, $curyear);
			
			$startarray[$firstday_curmonth] = date('F Y', $month);			
			$month = strtotime("+1 month", $month);		
		}
		return $startarray;
	}
	/**
	 * Generate an array for the last day of the months for which prices are available
	 * 
	 * @return Array 
	 */
	function getMonthsForEndFilter(){
		$config = Zend_Registry::get('config'); 
		$month = strtotime($config->commodityprice->firstmarketpricedate);
		$end = strtotime(getLastDayOfCurrentMonth());
		
		$endarray = array();
		while($month <= $end){		
			$curyear = date('Y', $month);
			$curmonth = date('n', $month);			
			$lastday_curmonth = getLastDayOfMonth($curmonth, $curyear);
			$endarray[$lastday_curmonth] = date('F Y', $month);			
			$month = strtotime("+1 month", $month);		
		}
		$endarray = array_reverse($endarray, true);
		return $endarray;
	}
	/** 
	 * Generate an array containing the dates for the market report analysis report going back 1 year
	 * 
	 * @return Array 
	 */
	function getMarketAnalysisReportDates() {
		$reportdate = new DateTime('this Monday'); 
		$config = Zend_Registry::get('config'); 
		$values = array(); 
		for ($i = 1; $i < 52; $i++) {
			$values[$reportdate->format($config->dateandtime->mediumformat)] = $reportdate->format($config->dateandtime->mediumformat);
			// reduce by one week
			$reportdate->modify('-7 day'); 
		}
	
		return $values; 
	}
	/**
	 * Generate an array containing the volumes for the market report analysis report. The use of a minvolume and maxvolume parameters
	 * enable the control over which data is entered to reduce data entry errors
	 *
	 * @return Array
	 */
	function getMarketPriceAnalysisVolumes() {
		$config = Zend_Registry::get('config'); 
		$values = array();
		// debugMessage($config->marketpriceanalysisreport);
		for ($i = $config->marketpriceanalysisreport->minvolume; $i < $config->marketpriceanalysisreport->maxvolume; $i++) {
			 $values[$i] = $i;
		}
		// add the maximum volume which is not added above
		$values[$config->marketpriceanalysisreport->maxvolume] = $config->marketpriceanalysisreport->maxvolume;
		return $values;
	}
	/**
	 * Generate an array containing the numbers for each volume in the market report analysis report. Each volume has 24 reports  
	 *
	 * @return Array
	 */
	function getMarketPriceAnalysisNumbers() {
		$values = array(); 
		for ($i = 1; $i < 25; $i++) {
			$values[$i] = $i; 
		}
		return $values; 
	}
	
	/**
	 * Get the dropdown of crop sector quarters 
	 * 
	 * @return Array  
	 */
	function getYearQuartersList() {
		$quarters = array(
			"4:2011" => "Q4 2011",
			"3:2011" => "Q3 2011",
			"2:2011" => "Q2 2011",
			"1:2011" => "Q1 2011",		
			"4:2010" => "Q4 2010",
			"3:2010" => "Q3 2010",
			"2:2010" => "Q2 2010",
			"1:2010" => "Q1 2010",		
		);
		return $quarters;
	}
	/**
	*	 Return the landing sites available in a district
	**/
	function getLandingSitesForLocation($districtid){
		$custom_query = '';
		if(!isEmptyString($districtid)){
			$custom_query = " AND districtid = '".$districtid."' ";
		}
		$valuesquery = "SELECT id AS optionvalue, `name` as optiontext FROM landingsite WHERE id <> '' ".$custom_query." ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($valuesquery);
}
	/**
	* Return the waterbodies available
	**/
	function getWaterBodies(){
		$valuesquery = "SELECT id AS optionvalue, `name` as optiontext FROM waterbody WHERE id <> '' ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($valuesquery);
	}	
	/**
	* Return the landing sites available in a district
	**/
	function getBoatsForLocation($districtid){
		$custom_query = '';
		if(!isEmptyString($districtid)){
			$custom_query = " AND l.districtid = '".$districtid."' ";
		}
		$valuesquery = "SELECT b.id AS optionvalue, concat(b.`regno`, '-', b.`name`) as optiontext FROM boat as b inner join landingsite as l on (b.landingsiteid = l.id) WHERE b.id <> '' ".$custom_query." ORDER BY optiontext";
		return getOptionValuesFromDatabaseQuery($valuesquery);
	}
	/**
	* Return the landing sites available in a district
	*/
	function getDistrictForLandingSite($landingsiteid){
		$valuesquery = "SELECT l.id AS optionvalue, l.`name` as optiontext FROM location as l inner join landingsite as ld on (ld.districtid = l.id) WHERE ld.id = '".$landingsiteid."' ORDER BY optiontext";
		// debugMessage($valuesquery);
		return getOptionValuesFromDatabaseQuery($valuesquery);
	}
	
?>