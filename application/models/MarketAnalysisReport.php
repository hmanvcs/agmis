<?php

/**
 * Weekly market analysis report which is uploaded  
 *
 */

class MarketAnalysisReport extends BaseEntity {
	
public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
	
		$this->setTableName('marketanalysisreport'); 
		
		$this->hasColumn('highlights', 'clob', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('reportdate', 'date', null, array('notnull' => true, 'notblank' => true, 'unique' => true));
		$this->hasColumn('volume', 'integer', null, array('notnull' => true, 'notblank' => true)); 
		$this->hasColumn('number', 'integer', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('filename', 'string', 255, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('filesize', 'integer', null, array('notnull' => true, 'notblank' => true));
	}
	/**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		// date fields
		$this->addDateFields(array('reportdate')); 
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"highlights.notblank" => $this->translate->_("marketanalysisreport_highlights_error"),	
       									"reportdate.notblank" => $this->translate->_("marketanalysisreport_reportdate_error"),
       									"reportdate.unique" => $this->translate->_("marketanalysisreport_reportdate_unique_error"),
						       			"volume.notblank" => $this->translate->_("marketanalysisreport_volume_error"),
						       			"number.notblank" => $this->translate->_("marketanalysisreport_number_error"),
						       			"filename.notblank" => $this->translate->_("marketanalysisreport_filename_error"),
						       			"filesize.notblank" => $this->translate->_("marketanalysisreport_filesize_error")
       	       						));
	}
	function validate() {
		parent::validate(); 
		
		// the volume and the number must be unique 
		$volume_number_query = "SELECT reportdate FROM marketanalysisreport WHERE volume = '".$this->getVolume()."' AND number = '".$this->getNumber()."' AND id <> '".$this->getID()."'";
		$conn = Doctrine_Manager::connection(); 
		$result = $conn->fetchOne($volume_number_query); 
		if(!isEmptyString($result)){
			$this->getErrorStack()->add("volume_number.unique", sprintf($this->translate->_("marketanalysisreport_unique_volume_number_error"), $this->getVolume(), $this->getNumber(), changeMySQLDateToPageFormat($result)));
		}
	}
	
	/**
	 * Build an HTML link to download the report
	 * 
	 * @param String $report_name A custom report name when needed  
	 * 
	 * @return String 
	 */
	function getLink($report_name = "") {
		$view = new Zend_View(); 
		if (isEmptyString($report_name)) {
			$report_name = $this->getDefaultReportName(); 
		} 
		 
		$filesize = new Zend_Measure_Binary($this->getFileSize(), Zend_Measure_Binary::BYTE); 
		// change to KB
		$filesize->setType(Zend_Measure_Binary::KILOBYTE); 
		$html = '<a href="'.$view->serverUrl($view->baseUrl('download/marketanalysisreport/filename/'.$this->getFileName())).'" target="_blank" title="Download '.$report_name.'">';
		$html .= $report_name.' (<img src="'.$view->serverUrl($view->baseUrl('images/adobereader.png')).'" />'.$filesize->toString(0).')'; 
		$html .= "</a>"; 
		return $html; 
	}
	
	function afterSave() {
		$new_file_name = "MarketAnalysisReport-".date("dFY", strtotime($this->getReportDate())).".pdf"; 
		// rename the file
		if (rename(APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'marketanalysisreport'.DIRECTORY_SEPARATOR.$this->getFileName(), APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'marketanalysisreport'.DIRECTORY_SEPARATOR.$new_file_name)) {
			$this->setFileName($new_file_name); 
			$this->save(); 
		}	
	}
	/**
	 * Generate the default name of the report
	 * 
	 * @return String 
	 * 
	 */
	function getDefaultReportName() {
		return 'Market Analysis Report for '.date('F d, Y', strtotime($this->getReportDate()));
	}
	
}

?>