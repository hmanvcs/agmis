<?php

class DownloadController extends IndexController {
	/**
	 * Action to download the market analysis report 
	 * 
	 */
	public function marketanalysisreportAction() {
		// automatic file mime type handling
		$filename = $this->_getParam('filename');
		$full_path = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'marketanalysisreport'.DIRECTORY_SEPARATOR.$filename;
		
		// file headers to force a download
		header('Content-Description: File Transfer');
		header('Content-Type: application/pdf');
		// to handle spaces in the file names
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		readfile($full_path);
		
		// disable layout and view
		$this->view->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	}

}
