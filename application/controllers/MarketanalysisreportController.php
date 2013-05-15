<?php

class MarketanalysisreportController extends SecureController {
	/* (non-PHPdoc)
	 * @see SecureController::getResourceForACL()
	 */
	public function getResourceForACL() {
		// TODO Auto-generated method stub
		return "Market Analysis Report"; 
	}
	
	public function createAction() {
		// reports are only uploaded on new but not for updates 
		if (isEmptyString($this->_getParam('id'))) {
			// Only Upload if document for upload exists
			$config = Zend_Registry::get("config");
			$this->_translate = Zend_Registry::get("translate");
			$session = SessionWrapper::getInstance();
		
			// only upload a file if the attachment field is specified
			$upload = new Zend_File_Transfer();
			$upload->setOptions(array('useByteString' => false));
		
			// only PDF file uploads are allowed 
			$upload->addValidator('Extension', false, "pdf");
		
			// the report before upload
			$file = $upload->getFileInfo('report');
			$oldfilename = $file['report']['name'];
		
			// base path for uploaded
			$destination_path = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'marketanalysisreport'.DIRECTORY_SEPARATOR;
		
			// the destination for the uploaded file   
			$upload->setDestination($destination_path);
		
			// rename the base logo file name
			$cur_timestamp = mktime();
			$newfilename = "tmp".time().".".findExtension($oldfilename);
		
			// rename the file to be uploaded using the set timestamp
			$upload->addFilter('Rename',  array('target' => $destination_path.$newfilename, 'overwrite' => true));
		
			// process the file upload
			if($upload->receive()){
				// file has been sucessfully uploaded, set the filename and filesize 
				$this->_setParam('filename', $newfilename);
				$this->_setParam('filesize', $upload->getFileSize());
				parent::createAction(); 
			} else {
				$uploaderrors = $upload->getMessages();
				$customerrors = array();
				if(!isArrayKeyAnEmptyString('fileExtensionFalse', $uploaderrors)){
					$customerrors['fileExtensionFalse'] = sprintf($this->_translate->translate('marketanalysisreport_invalid_ext_error'), $oldfilename, 'PDF');
				}
		
				$session->setVar(ERROR_MESSAGE, 'The following errors occured <ul><li>'.implode('</li><li>', $customerrors).'</li></ul>');
				$session->setVar(FORM_VALUES, $this->_getAllParams());
				// return to index page
				$this->_helper->redirector->gotoUrl(decode($this->_getParam(URL_FAILURE)));
			}
		} else {
			parent::createAction(); 
		}
	}


	function deleteAction() {
		$this->_setParam('entityname', 'MarketAnalysisReport');
		$this->_setParam(SUCCESS_MESSAGE, 'marketanalysisreport_delete_success');
		$this->_setParam(URL_SUCCESS, encode($this->view->baseUrl($this->getControllerName()."/list")));
		parent::deleteAction();
	}
	
}