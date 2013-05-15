<?php

# functions to create and manage drop down lists
require_once 'dropdownlists.php';

define("ACTION_CREATE", "create");
define("ACTION_INDEX", "index"); // maps to the default controller for Zend Framework, same as the create action in the ACL 
define("ACTION_EDIT", "edit");
define("ACTION_APPROVE", "approve");
define("ACTION_DELETE", "delete");
define("ACTION_EXPORT", "export");
define("ACTION_VIEW", "view");
define("ACTION_LIST", "list");

# redirect, success and error urls during the processing of an action 
define("URL_REDIRECT", "redirecturl"); // url forwarded to when a user has to login 
define("URL_SUCCESS", "successurl"); // override the url when an action suceeds
define("URL_FAILURE", "failureurl"); // override the url when an action fails

# the separator between a table name and a column name for filtering since the . cannot be used as
# a separator for HTML field names
define("HTML_TABLE_COLUMN_SEPARATOR", "__");

# the session variable holding the values from the REQUEST when an error occurs
define("FORM_VALUES", "formvalues");
# the session variable holding the error message when processing a form 
define("ERROR_MESSAGE", "errors"); 
# the session variable holding the success message when processing a form 
define("SUCCESS_MESSAGE", "successmessage"); 
# the session variable holding the error message for the error page which is not cleared from page to page 
define("APPLICATION_ERROR_PAGE_MESSAGE", "error_page_erros"); 
# the session variable for the access control lists 
define("SESSION_ACL", "acl"); 

# calendar view options
define("CALENDAR_VIEW_MONTH", "month_view"); 
define("CALENDAR_VIEW_WEEK", "week_view"); 

# constant for showing views in a popup
define("PAGE_CONTENTS_ONLY", "pgc");
define("EXPORT_TO_EXCEL", "excel"); 

# constant for the select chain value
define("SELECT_CHAIN_TYPE", "select_chain_type"); 

# excel generation constants
# a comma delimited list of column indexes with numbers
define("EXPORT_NUMBER_COLUMN_LIST", "numbercolumnlist");
# the number of columns to ignore at the beginning of the query 
define("EXPORT_IGNORE_COLUMN_COUNT", "columncheck");  
# the query string with all the results
define("ALL_RESULTS_QUERY", "arq");
# the query string with the searches and filters applied
define("CURRENT_RESULTS_QUERY", "crq");
# the page title for current list
define("PAGE_TITLE", "ttl");

// application types
define("APPLICATION_AGMIS", 0); 
define("APPLICATION_LAMIS", 1);
define("APPLICATION_LGMIS", 2);

/**
 * Change a date from MySQL database Format (yyyy-mm-dd) to the format displayed on pages(mm/dd/yyyy)
 * 
 * If the date from the database is NULL, it is transformed to an empty string for display on the pages 
 *
 * @param String $mysqldate The date in MySQL format 
 * @return String the date in short date format, or an empty string if no date is provided 
 */
function changeMySQLDateToPageFormat($mysqldate) {
	$aconfig = Zend_Registry::get("config"); 
	if (isEmptyString($mysqldate)) {
		return $mysqldate;
	} else {
		return date($aconfig->dateandtime->mediumformat, strtotime($mysqldate));
	}
}
/**
 * Transform a date from the format displayed on pages(mm/dd/yyyy) to the MySQL database date format (yyyy-mm-dd). 
 * If the date from the database is an empty string or the string NULL, it is transformed to a NULL value.
 *
 * @param String $pagedate The string representing the date
 * @return String The MYSQL datetime format or NULL if the provided date is an empty string or the string NULL 
 */
function changeDateFromPageToMySQLFormat($pagedate) {
	if ($pagedate == "NULL") {
		return NULL;
	}
	if (isEmptyString($pagedate)) {
		return NULL;
	} else {
		return date("Y-m-d H:i:s", strtotime($pagedate));
	}
}


/**
 * Check whether or not the string is empty. The string is emptied
 *
 * @param String $str The string to be checked
 * 
 * @return bool Whether or not the string is empty
 */
function isEmptyString($str) {
	if ($str == "") {
		return true; 
	}
	if ((trim($str)) == "") {
		return true;
	} else {
		return false;
	}
}

/**
 * Check whether or not the value of the key in the specified array is empty
 *
 * @param String $key The key whose value is to be checked
 * @param Array $arr The array to check  
 * 
 * @return bool Whether or not the array key is empty
 */
function isArrayKeyAnEmptyString($key, $arr) {	
	if (!array_key_exists($key, $arr)) {		
		return true; 
	}
	if (is_string($arr[$key])) {
		return isEmptyString($arr[$key]);
	}
	return false; 
}
/**
 * Check whether or not the string is empty. The string is emptied
 *
 * @param String $str The string to be checked
 * 
 * @return boolean Whether or not the string is empty
 */
function isNotAnEmptyString($str) {
	return ! isEmptyString($str);
}

/**
 * Return a debug message with a break tag before and two break tags after
 *
 * @param Object $obj The object to be printed
 */
function debugMessage($obj) {
	echo "<br />";
	print_r($obj);
	echo "<br /><br />";
}

/**
 * Return the value of the checked HTML attribute for a checkbox or radio button
 *
 * @param bool $bool whether or not the HTML control is checked
 * @return String the checked attribute
 */
function getCheckedAttribute($bool) {
	if ($bool) {
		return ' checked="checked"';
	}
	return "";
}
/**
 *  Merge the arrays passed to the function and keep the keys intact.
 *  If two keys overlap then it is the last added key that takes precedence.
 * 
 * @return Array the merged array
 */
function array_merge_maintain_keys() {
	$args = func_get_args();
	$result = array();
	foreach ( $args as &$array ) {
		foreach ( $array as $key => &$value ) {
			$result[$key] = $value;
		}
	}
	return $result;
}

# function that trims every value of an array
function trim_value(&$value) {
	$value = trim($value);
}

/**
 * Recursively Remove empty values from an array. If any of the keys contains an 
 * array, the values are also removed.
 *
 * @param Array $input The array
 * @return Array with the specified values removed or the filtered values
 */
function array_remove_empty($arr) {
	$narr = array();
	while ( list ($key, $val) = each($arr) ) {
		if (is_array($val)) {
			$val = array_remove_empty($val);
			// does the result array contain anything?
			if (count($val) != 0) {
				// yes :-)
				$narr[$key] = $val;
			}
		} else {
			if (! isEmptyString($val)) {
				$narr[$key] = $val;
			}
		}
	}
	unset($arr);
	return $narr;

}

/**
 * Send test email
 *
 * @param String $subject The subject of the email 
 * @param String $message The contents of the email 
 */
function sendTestMessage($subject = "", $message = "") {
	$mailer = getMailInstance(); 
	
	# get an instance of the PHP Mailer
	$from_email = $mailer->getDefaultFrom(); 
	$mailer->AddTo($from_email['email']);
	
	$mailer->setSubject($subject);
	$mailer->setBodyHTML($message);
	try {
		$result = $mailer->send();
		//debugMessage("The email sending result is ".$result);
		if (! $result) {
			# Log the error
			echo "an error occured while sending the message " . $mailer->ErrorInfo;
		}
	} catch ( Exception $e ) {
		debugMessage("Error sending email ".$e);
	}
}
/**
 * Wrapper function for the encoding of the urls using base64_encode 
 *
 * @param String $str The string to be encoded
 * @return String The encoded string 
 */
function encode($str) {
	return base64_encode($str); 
}
/**
 * Wrapper function for the decoding of the urls using base64_decode 
 *
 * @param String $str The string to be decoded
 * @return String The encoded string 
 */
function decode($str) {
	return base64_decode($str); 
}

/**
 * Function to generate a JSON string from an array of data, using the keys and values
 *
 * @param $data The data to be converted into a string
 * @param $default_option_value Value of the default option
 * @param $default_option_text Test for the default 
 * 
 * @return the JSON string containing the select options
 */
function generateJSONStringForSelectChain($data, $default_option_value = "", $default_option_text = "<Select One>") {
	$values = array(); 
	//debugMessage($data);
	if (!isEmptyString($default_option_value)) {
		# use the text and option from the data
		if(!isArrayKeyAnEmptyString($default_option_value, $data)){
			$values[] = '{"id":"' . $default_option_value . '", "label":"' . $data[$default_option_value] . '"}';
			// remove the default option from the available options
			unset($data[$default_option_value]);
		}
	}
	# add a default option
	$values[] = '{"id":"", "label":"' . $default_option_text . '"}';
	foreach ( $data as $key => $value ) {
		$values[] = '{"id":"'.$key.'", "label":"' . $value . '"}';
		//debugMessage($strstring);
	}
	# remove the first comma at the end
	return '[' . implode("," , $values). "]";
}
/**
 * Format a number to two decimal places and a comma separator between thousands. Empty Strings are considered to be numeric
 *
 * @param Number $number The number to be formatted
 * @return Number The formatted version of the number
 */
function formatNumber($number) {
	if (isEmptyString($number) || !is_numeric($number)) {
		return $number;
	}
	$aconfig = Zend_Registry::get("config"); 
	return number_format($number, $aconfig->currency->decimalplaces);
}
/**
 * Generate an HTML list from an array of values
 *
 * @param Array $array
 * @return String 
 */
function createHTMLListFromArray($array) {
	$str = ""; 
	// return empty string if no array is passed
	if (!is_array($array)) {
		return $str; 
	}
	// return an empty string if the array is empty
	if (!$array) {
		return $str; 
	}
	
	// opening list tag and the first li element
	$str  = "<ul><li>";
	// explode the array and generate the inner list items
	$str .= implode($array, "</li><li>");
	// close the last list item, and the ul
	$str .= "</li></ul>"; 
	
	return $str; 
}
function createHTMLCommaListFromArray($array, $separator = "', '") {
	$str = ""; 
	// return empty string if no array is passed
	if (!is_array($array)) {
		return $str; 
	}
	// return an empty string if the array is empty
	if (!$array) {
		return $str; 
	}
	
	// explode the array and generate the inner list items
	$str .= implode($array, $separator);
	
	return $str; 
}
/**
  * Load the application configuration from the database
  * 
  */
function loadConfig() {
	$cache = Zend_Registry::get('cache');
	// load the configuration from the cache
	$config = $cache->load('config'); 
	if (!$config) {
		// do nothing 
	} else {
		// add the config object to the registry 
		Zend_Registry::set('config', $config);
		return; 
	}
	
	// load the active application configuration from the database
	$sql = "SELECT section, optionname, optionvalue FROM appconfig WHERE active = 'Y'";

	$conn = Doctrine_Manager::connection(); 
	$result = $conn->fetchAll($sql); 
	
	// generate a config array from the data
	if (!$result) {
		// do nothing no data returned
	} else {
		$config_array = array(); 
		foreach ($result as $line) {
			if (isEmptyString($line['section'])) {
				// no section name provided so add the option to the root
				$config_array[$line['optionname']] = $line['optionvalue']; 
			} else {
				// add the option to the section 
				$config_array[$line['section']][$line['optionname']]= $line['optionvalue'];
			}  
		}
		# Add the config object to the registry
		$config = new Zend_Config($config_array); 
		Zend_Registry::set('config', $config);
		# cache the config object
		$cache->save($config, 'config');
	}
}
/**
 * Create a Zend_Mail instance from the registry, clear all recipients and the existing subject
 * 
 * @return Zend_Mail 
 */
function getMailInstance() {
	// create mail object
	$mail = Zend_Registry::get("mail");
	//TODO: Temporary workaround for subject set twice error message
	// clear the subject to prevent an error when sending multiple emails in the same session
	$mail->clearSubject(); 
	// clear the addresses too
	$mail->clearRecipients();
	
	return $mail; 
}
/**
 * Return an instance of the access control list 
 *
 * @return ACL 
 */
function getACLInstance() {
	$cache = Zend_Registry::get('cache'); 
	$session = SessionWrapper::getInstance(); 
	// check if the acl is cached
	$aclkey = "acl".$session->getVar('userid'); 
	$acl = $cache->load($aclkey); 
	if (!$acl) {
		$acl = new ACL($session->getVar('userid')); 
	}
	
	return $acl; 
}
/**
 * Return the file extension from a file name
 * @param string $filename
 * @return The file extension 
 */
function findExtension($filename){  
	return substr(strrchr($filename,'.'),1);
}
/**
 * Whether or not the application is AGMIS
 * 
 * @return TRUE if the application is AGMIS and FALSE if otherwise 
 */
function isApplicationAGMIS() {
	$session = SessionWrapper::getInstance(); 
	if (intval($session->getVar('applicationtype')) == APPLICATION_AGMIS || isEmptyString(intval($session->getVar('applicationtype')))) {
		return true; 
	}
	return false; 
}
/**
 * Whether or not the application is LAMIS
 * 
 * @return TRUE if the application is LAMIS and FALSE if otherwise 
 */
function isApplicationLAMIS() {
	$session = SessionWrapper::getInstance(); 
	if (intval($session->getVar('applicationtype')) == APPLICATION_LAMIS) {
		return true; 
	}
	return false; 
}
/**
 * Whether or not the application is LGMIS
 * 
 * @return TRUE if the application is LGMIS and FALSE if otherwise 
 */
function isApplicationLGMIS() {
	$session = SessionWrapper::getInstance(); 
	if (intval($session->getVar('applicationtype')) == APPLICATION_LGMIS) {
		return true; 
	}
	return false; 
}
	/**
	 * Decode all html entities of an array  
	 * @param Array $elem the array to be decoded
	 */
	function decodeHtmlEntitiesInArray(&$elem){ 
  		if (!is_array($elem)) { 
    		$elem=html_entity_decode($elem); 
		}  else  { 
			foreach ($elem as $key=>$value){
				$elem[$key]=decodeHtmlEntitiesInArray($value);
			} 
  		} 
		return $elem; 
	}
	
/**
 * Trims a given string with a length more than a specified length with a more link to view the details 
 *
 * @param string $text
 * @param int $length
 * @param string $tail
 * 
 * @return string the substring with a more link as the tail
 */
function snippet($text, $length, $tail = "") {
	$text = trim($text);
	$txtl = strlen($text);
	if ($txtl > $length) {
		for($i = 1; $text[$length - $i] != " "; $i ++) {
			if ($i == $length) {
				return substr($text, 0, $length) . $tail;
			}
		}
		for(; $text[$length - $i] == "," || $text[$length - $i] == "." || $text[$length - $i] == " "; $i ++) {
			;
		}
		$text = substr($text, 0, $length - $i + 1) . $tail;
	}
	return $text;
} 

/**
 * Return an array containing the start and end dates of the quarter 
 * 
 * @param String $quarter containing the quarter and the year in the format quarter:year
 * 
 * @return Array 
 */
function getDatesForQuarter($quarter){
	$quarter_data = explode(":", $quarter); 
	
	$startdates = array(1 => "Jan 1", 2 => "Apr 1", 3 => "July 1", 4 => "Oct 1"); 
	$enddates = array(1 => "Mar 31", 2 => "June 30", 3 => "Sep 30", 4 => "Dec 31"); 
	
	return array("startdate" => $startdates[$quarter_data[0]]." ".$quarter_data[1], "enddate" => $enddates[$quarter_data[0]]." ".$quarter_data[1]); 
}
/**
 * 
 * Return the quarter of the year for a given date
 * @param string $date
 * @return string of quarter:year
 */
function getQuarterFromDate($date){
	if(isEmptyString($date)){
		return; 
	}
	$str = strtotime($date);
	$tm = mktime(0, 0, 0, date('n', $str), date('j', $str), date('Y', $str));
	$qtr = ceil(date("m", $tm)/3);
	return $qtr.":".date('Y', $str);
}
?>