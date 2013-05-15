<?php

class UserAccount extends BaseEntity {
	public function setTableDefinition() {
		#add the table definitions from the parent table
		parent::setTableDefinition();
		
		$this->setTableName('useraccount');
		$this->hasColumn('firstname', 'string', 255, array( 'notnull' => true, 'notblank' => true));
		$this->hasColumn('lastname', 'string', 255, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('othername', 'string', 255);
		$this->hasColumn('email', 'string', 50, array('unique' => true, 'notnull' => true, 'notblank' => true));
		$this->hasColumn('phonenumber', 'string', 15);
		$this->hasColumn('phonenumber2', 'string', 15);
		$this->hasColumn('password', 'string', 50);
		$this->hasColumn('notes', 'string', 1000);
		$this->hasColumn('securityquestion', 'string', 255);
		$this->hasColumn('securityanswer', 'string', 255);
		$this->hasColumn('changepassword', 'enum', null, array('values' => array(0 => 'Y', 1 => 'N'), 'default' => 'Y'));
		$this->hasColumn('activationkey', 'string', 50);
		$this->hasColumn('isactive', 'enum', null, array('values' => array(1 => 'Y', 0 => 'N'), 'default' => 'Y'));
		$this->hasColumn('nextpasswordchangedate','date');	
		$this->hasColumn('lastlogindate','date');
		$this->hasColumn('loginretries', 'integer');
		$this->hasColumn('locationid', 'integer');	
		$this->hasColumn('marketid', 'integer');
		$this->hasColumn('applicationtype', 'integer', null, array('default' => APPLICATION_AGMIS));
		$this->hasColumn('organisationid', 'integer');
		$this->hasColumn('agreedtoterms', 'integer');
		$this->hasColumn('createdby', 'integer');
		$this->hasColumn('activationdate', 'date');
	}
	/**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	public function construct() {
		parent::construct();
		// set the custom error messages
       	$this->addCustomErrorMessages(array(
       									"firstname.notblank" => $this->translate->_("useraccount_firstname_error"),
       									"lastname.notblank" => $this->translate->_("useraccount_lastname_error"),
       									"email.notblank" => $this->translate->_("useraccount_email_error"),
       									"email.email" => $this->translate->_("useraccount_email_invalid_error")
       	       						));
	}
	
	public function setUp() {
		parent::setUp(); 
		// copied directly from BaseEntity since the createdby can be NULL when a user signs up 
		// automatically set timestamp column values for datecreated and lastupdatedate 
		$this->actAs('Timestampable', 
						array('created' => array(
												'name' => 'datecreated',    
											),
							 'updated' => array(
												'name'     =>  'lastupdatedate',    
												'onInsert' => false,
												'options'  =>  array('notnull' => false)
											)
						)
					);
		$this->hasMany('UserGroup as usergroups',
							array('local' => 'id',
									'foreign' => 'userid'
							)
						);
		$this->hasOne('UserAccount as creator', 
								array(
									'local' => 'createdby',
									'foreign' => 'id',
								)
						);
		$this->hasOne('PriceSource as market',
						 array(
								'local' => 'marketid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('District as location',
						 array(
								'local' => 'locationid',
								'foreign' => 'id'
							)
					); 
		$this->hasOne('Organisation as organisation',
				array(
						'local' => 'organisationid',
						'foreign' => 'id'
				)
		);
	}
	/*
	 * 
	 */
	function validate() {
		// add the address for the unique email
		$this->addCustomErrorMessages(array("email.unique" => sprintf($this->translate->_("useraccount_email_unique_error"), $this->getEmail())));
		// execute the column validation 
		parent::validate();

		// check that at least one group has been specified
		if ($this->getUserGroups()->count() == 0) {
			$this->getErrorStack()->add("groups", $this->translate->_("useraccount_group_error"));
		}
	}
	/*
	 * 
	 */
	function processPost($formvalues){
		// if the passwords are not changed , set value to null
		if(isArrayKeyAnEmptyString('password', $formvalues)){
			unset($formvalues['password']); 
		} else {
			$formvalues['password'] = sha1($formvalues['password']); 
		}
		# force setting of default none string column values. enum, int and date 	
		if(isArrayKeyAnEmptyString('nextpasswordchangedate', $formvalues)){
			unset($formvalues['nextpasswordchangedate']); 
		}
		if(isArrayKeyAnEmptyString('changepassword', $formvalues)){
			$formvalues['changepassword'] = "Y"; 
		}
		if(isArrayKeyAnEmptyString('lastlogindate', $formvalues)){
			unset($formvalues['lastlogindate']); 
		}
		if(isArrayKeyAnEmptyString('loginretries', $formvalues)){
			unset($formvalues['loginretries']); 
		}
		if(isArrayKeyAnEmptyString('isactive', $formvalues)){
			$formvalues['isactive'] = "Y"; 
		}
		if(isArrayKeyAnEmptyString('locationid', $formvalues)){
			unset($formvalues['locationid']); 
		}
		if(isArrayKeyAnEmptyString('marketid', $formvalues)){
			unset($formvalues['marketid']); 
		}
		if(isArrayKeyAnEmptyString('organisationid', $formvalues)){
			unset($formvalues['organisationid']);
		}
		// move the data from $formvalues['usergroups_groupid'] into $formvalues['usergroups'] array
		// the key for each group has to be the groupid
		if (array_key_exists('usergroups_groupid', $formvalues)) {
			$groupids = $formvalues['usergroups_groupid']; 
			$usergroups = array(); 
			foreach ($groupids as $id) {
				$usergroups[]['groupid'] = $id; 
			}
			$formvalues['usergroups'] = $usergroups; 
			// remove the usergroups_groupid array, it will be ignored, but to be on the safe side
			unset($formvalues['usergroups_groupid']); 
		}
		
		// add the userid if the useraccount is being edited
		if (!isArrayKeyAnEmptyString('id', $formvalues)) {
			if (array_key_exists('usergroups', $formvalues)) {
				$usergroups = $formvalues['usergroups']; 
				foreach ($usergroups as $key=>$value) {
					$formvalues['usergroups'][$key]["userid"] = $formvalues["id"];
				}
			} 
		}
		parent::processPost($formvalues);
	}	
	
	function afterSave() {
		$this->sendSignupNotification(); 
	}
	/**
	 * Process the activation key from the activation email
	 *
	 * @param $actkey The activation key
	 *
	 * @return bool TRUE if the signup process completes successfully, false if activation key is invalid or save fails
	 */
	function activateAccount($actkey) {
		# save to the audit trail
	
		# validate the activation key
		if($this->getActivationKey() != $actkey){
			# Log to audit trail when an invalid activation key is used to activate account
		$audit_values = array("executedby" => $this->getID(), "transactiontype" => USER_ACTIVATE, "success" => "N");
		$audit_values["transactiondetails"] = "Invalid activation key used in activating User ".$this->getFirstName()." ".$this->getLastName(). " (".$this->getEmail()."). ";
		$this->notify(new sfEvent($this, USER_ACTIVATE, $audit_values));
		$this->getErrorStack()->add("user.activationkey", $this->translate->_("useraccount_invalid_actkey_error"));
		return false;
	}
	
	# set active to true and blank out activation key
	$this->setIsActive("Y");
	$this->setActivationKey("");
	$this->setActivationDate(date("Y-m-d H:i:s"));
	
	# save
	try {
		$this->save();
	
		# Add to audittrail that a new user has been activated.
		$audit_values = array("executedby" => $this->getID(), "transactiontype" => USER_ACTIVATE, "success" => "Y");
		$audit_values["transactiondetails"] = $this->getName()." (".$this->getEmail().") has completed the sign up process";
		$this->notify(new sfEvent($this, USER_SIGNUP, $audit_values));
	
		return true;
	
	} catch (Exception $e){
		$this->getErrorStack()->add("user.activation", $this->translate->_("useraccount_activation_error"));
		$this->logger->err("Error activating useraccount ".$this->getEmail()." ".$e->getMessage());
		# log to audit trail when an error occurs in updating payee details on user account
		$audit_values = array("executedby" => $this->getID(), "transactiontype" => EMPLOYER_ACTIVATE, "success" => "N");
		$audit_values["transactiondetails"] = "An error occured in activating Payee ".$this->getFirstName()." ".$this->getLastName(). " (".$this->getEmail()."). ".$e->getMessage();
		$this->notify(new sfEvent($this, USER_SIGNUP, $audit_values));
		return false;
	}
	}
	/**
	 * Reset the password for  the user. All checks and validations have been completed
	 * 
	 * @param String $newpassword The new password. If the new password is not defined, a new random password is generated
	 *
	 * @return Boolean TRUE if the password is changed, FALSE if it fails to change the user's password.
	 */
	 function resetPassword($newpassword = "") {
	 	// check if the password is empty 
	 	if (isEmptyString($newpassword)) {
	 		// generate a new random password
	 		$newpassword = $this->generatePassword(); 
	 	}
	 	return $this->changePassword("", $newpassword, false); 
	}
	/**
	 * Change the password for the user. All checks and validations have been completed
	 * 
	 * @param String $providedpassword The password provided on the screen
	 * @param String $newpassword The new password
     * @param boolean $verify Verify whether the provided password is the same as the user's current password
	 *
	 * @return TRUE if the password is changed, FAlSE if it fails to change the user's password.
	 */
	function changePassword($providedpassword, $newpassword, $verify = true){
		// check if the provided password is the same as that for the user
      	if ($verify) {
          if ($this->getPassword() != sha1($providedpassword)) {
              $this->getErrorStack()->add("oldpassword.invalid", $this->translate->_("useraccount_oldpassword_invalid_error"));
              return false;
          }
     	}
		// now change the password
		$this->setPassword(sha1($newpassword));
      	$this->setActivationKey('');
      	$this->setNextPasswordChangeDate(date('Y-m-d', strtotime("+ ".$this->config->password->daystoexpire." days"))); 
      	
      	try {
      	$this->save();
      	
      	# Log to audit trail that a password has been changed.
			$audit_values = array("transactiontype" => USER_CHANGE_PASSWORD, "userid" => $this->getID(), "executedby" => $this->getID(), "success" => 'Y');
			$audit_values['transactiondetails'] = $this->getName()." changed account password";
			$this->notify(new sfEvent($this, USER_CHANGE_PASSWORD, $audit_values));
      	
      	} catch (Exception $e){
      		# Log to audit trail that user has failed to change password
			$audit_values = array("transactiontype" => USER_CHANGE_PASSWORD, "userid" => $this->getID(), "executedby" => $this->getID(), "success" => 'N');
			$audit_values['transactiondetails'] = $this->getName()." failed to change account password". $e->getMessage();
			$this->notify(new sfEvent($this, USER_CHANGE_PASSWORD, $audit_values));
      	}
		return true;
	}
	/*
	 * Reset the user's password and send a notification to complete the recovery  
	 *
	 * @return Boolean TRUE if resetting is successful and FALSE if emailaddress security questions and answer is invalid or has no record in the database
	 */
	function recoverPassword() {
      // save to the audit trail
		$audit_values = array("transactiontype" => USER_RECOVER_PASSWORD); 
		// set the updater of the account only when specified
		if (!isEmptyString($this->getLastUpdatedBy())) {
			$audit_values['executedby'] = $this->getLastUpdatedBy();
		}
		
		# check that the user's email exists and that they are signed up
		if(!$this->findByEmail($this->getEmail())){
			$audit_values['transactiondetails'] = "Recovery of password for '".$this->getEmail()."' failed - user not found";
			$this->notify(new sfEvent($this, USER_RECOVER_PASSWORD, $audit_values));
			return false;
		}
			
		# reset the password and set the next password change date
		$this->setNextPasswordChangeDate(date('Y-m-d')); 
		$this->setActivationKey($this->generateActivationKey());
		
		# save the activation key for the user 
		$this->save();
		
		# Send the user the reset password email
		$this->sendRecoverPasswordEmail();
		
		// save the audit trail record
		// the transaction details is the email address being used to
		$audit_values['userid'] = $this->getID(); 
		$audit_values['transactiondetails'] = "Password Recovery link for '".$this->getEmail()."' sent to '".$this->getEmail()."'";
		$audit_values['success'] = 'Y';
		$this->notify(new sfEvent($this, USER_RECOVER_PASSWORD, $audit_values));
		
		return true;
	}
	/**
	 * Send an email with a link to activate the members' account
	 */
	function sendRecoverPasswordEmail() {
		$template = new EmailTemplate(); 
		// create mail object
		$mail = Zend_Registry::get("mail");

		// assign values
		$template->assign('firstname', $this->getFirstName());
		// just send the parameters for the activationurl, the actual url will be built in the view 
		$template->assign('resetpasswordurl', array("controller"=> "user","action"=> "resetpassword", "actkey" => $this->getActivationKey(), "id" => encode($this->getID())));
		
		// configure base stuff
		$mail->addTo($this->getEmail());
		$mail->setSubject($this->translate->_('useraccount_email_subject_recoverpassword'));
		// render the view as the body of the email
		$mail->setBodyText($template->render('recoverpassword.phtml'));
		$mail->send();
		
		return true;
   }
	/**
    * Send a notification to a user that their account has been setup and instructions on how to activate it
    *
    * @return bool whether or not the signup notification email has been sent
    *
    */
   function sendSignupNotification() {
	   	$template = new EmailTemplate();
	   	// create mail object
	   	$mail = getMailInstance();
	   
	   	// assign values
	   	$template->assign('firstname', $this->getFirstName());
	   	// just send the parameters for the activationurl, the actual url will be built in the view
	   	$template->assign('activationurl', array("action"=> "activate", "actkey" => $this->getActivationKey(), "id" => encode($this->getID())));
	   
	   	// configure base stuff
	   	$mail->addTo($this->getEmail());
	   	$mail->setSubject($this->translate->_('useraccount_email_subject_signup'));
	   	// render the view as the body of the email
	   	$mail->setBodyText($template->render('signupnotification.phtml'));
	   	$mail->send();
	   
	   	return true;
   }
	/**
	 * Generate a new password incase a user wants a new password
	 * 
	 * @return String a random password 
	 */
    function generatePassword() {
		return $this->generateRandomString($this->config->password->minlength);
    }
	/**
	 * Generate a 10 digit activation key  
	 * 
	 * @return String An activation key
	 */
    function generateActivationKey() {
		return substr(md5(uniqid(mt_rand(), 1)), 0, 10);
    }
   /**
    * Find a user account either by their email address 
    * 
    * @param String $email The email
    * @return UserAccount or FALSE if the user with the specified email does not exist 
    */
	function findByEmail($email) {
		# query active user details using email
		$q = Doctrine_Query::create()->from('UserAccount u')->where('u.email = ?', $email);
		$result = $q->fetchOne(); 
		
		// check if the user exists 
		if(!$result){
			// user with specified email does not exist, therefore is valid
			$this->getErrorStack()->add("user.invalid", $this->translate->_("useraccount_user_invalid_error"));
			return false;
		}
		
		$data = $result->toArray(); 

		// merge all the data including the user groups 
		$this->merge($data);
		// also assign the identifier for the object so that it can be updated
		$this->assignIdentifier($data['id']); 
		
		return true; 
	}
	/**
	 * Return the user's full names, which is a concatenation of the first and last names
	 *
	 * @return String The full name of the user
	 */
	function getName() {
		return $this->getFirstName()." ".$this->getLastName();
	}		
	/*
	 * TODO Put proper comments
	 */
	function generateRandomString($length) {
		$rand_array = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9");
		$rand_id = "";
		for ($i = 0; $i <= $length; $i++) {
			$rand_id .= $rand_array[rand(0, 35)];
		}
		return $rand_id;
	}
 	/**
     * Return an array containing the IDs of the groups that the user belongs to
     *
     * @return Array of the IDs of the groups that the user belongs to
     */
    function getGroupIDs() {
        $ids = array();
        $groups = $this->getUserGroups();
        //debugMessage($groups->toArray());
        foreach($groups as $thegroup) {
            $ids[] = $thegroup->getGroupID();
        }
        return $ids;
    }
    /**
     * Display a list of groups that the user belongs
     *
     * @return String HTML list of the groups that the user belongs to
     */
    function displayGroups() {
        $groups = $this->getUserGroups();
        $str = "";
        if ($groups->count() == 0) {
            return $str;
        }
        $str .= '<ul class="list">';
        foreach($groups as $thegroup) {
            $str .= "<li>".$thegroup->getGroup()->getName()."</li>"; 
        }
        $str .= "</ul>";
        return $str; 
    }
    /**
     * The full text of the application type
     * 
     * @return String 
     */
    function getApplicationTypeDescription() {
    	return LookupType::getLookupValueDescription("APPLICATION_TYPE", $this->getApplicationType()); 
    }
	
}
?>
