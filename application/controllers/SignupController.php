<?php

class SignupController extends IndexController {

	function signupAction() {
		// the group to which the user is to be added
		$this->_setParam("usergroups_groupid", array(8));
		$this->_setParam('entityname', 'UserAccount');
		$this->_setParam('agreedtoterms', '0');
		$this->_setParam('isactive', 'N');
		$this->_setParam(URL_SUCCESS, encode($this->view->baseUrl("signup/confirm")));
		$this->_setParam(URL_FAILURE, encode($this->view->baseUrl("signup")));
		$this->_setParam("action", ACTION_CREATE);
		
		parent::createAction(); 
	}
	
	function confirmAction(){}
	
	/**
	 * Active the useraccount
	 */
	function activateAction() {
		$user = new UserAccount();
		$user->populate(decode($this->_getParam("id")));
		$this->view->result = $user->activateAccount($this->_getParam('actkey'));
		if (!$this->view->result) {
			// activation failed
			$this->view->message = $user->getErrorStackAsString();
		}
	}
}

?>