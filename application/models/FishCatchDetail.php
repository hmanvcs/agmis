<?php

/**
 * The relationship between fish catch summary and Details
 */
class FishCatchDetail extends BaseRecord  {
	public function setTableDefinition() {
		parent::setTableDefinition();
		$this->setTableName('fishcatchdetail');
		
		$this->hasColumn('id', 'integer', 11, array('primary' => true, 'autoincrement' => true));
		$this->hasColumn('catchsummaryid', 'integer', 11, array("notblank" => true));
		$this->hasColumn('boatid', 'integer', 11, array("notblank" => true));
		$this->hasColumn('nettype',  'tinyint', null, array('notnull' => true, 'notblank' => true));
		$this->hasColumn('hooksize', 'decimal', 11, array('notnull' => true, 'notblank' => true, 'default' => '0'));
		$this->hasColumn('hookno',  'tinyint', null, array('notnull' => true, 'notblank' => true, 'default' => '0'));
		$this->hasColumn('mputaid', 'integer', null);
		$this->hasColumn('mputacount',  'tinyint', null, array('default' => '0'));
		$this->hasColumn('mputaweight', 'decimal', null, array('default' => '0'));
		$this->hasColumn('ngegeid', 'integer', null);
		$this->hasColumn('ngegecount',  'tinyint', null, array('default' => '0'));
		$this->hasColumn('ngegeweight', 'decimal', null, array('default' => '0'));
		$this->hasColumn('mukeneid', 'integer', null);
		$this->hasColumn('mukenecount',  'tinyint', null, array('default' => '0'));
		$this->hasColumn('mukeneweight', 'decimal', null, array('default' => '0'));
		$this->hasColumn('mambaid', 'integer', null);
		$this->hasColumn('mambacount',  'tinyint', null, array('default' => '0'));
		$this->hasColumn('mambaweight', 'decimal', null, array('default' => '0'));
		$this->hasColumn('maleid', 'integer', null);
		$this->hasColumn('malecount', 'tinyint', null, array('default' => '0'));
		$this->hasColumn('maleweight', 'decimal', null, array('default' => '0'));
		$this->hasColumn('othertilapiaid', 'integer', null);
		$this->hasColumn('othertilapiacount', 'tinyint', null, array('default' => '0'));
		$this->hasColumn('othertilapiaweight', 'decimal', null, array('default' => '0'));
		$this->hasColumn('otherid', 'integer', null);
		$this->hasColumn('othercount', 'tinyint', null, array('default' => '0'));
		$this->hasColumn('otherweight', 'decimal', null, array('default' => '0'));
		
	}
	 /**
	 * Contructor method for custom functionality - add the fields to be marked as dates
	 */
	function construct() {
		// initialize parent settings
		parent::construct();
		
		// define the custom error messages
       	$this->addCustomErrorMessages(array(
       									"boatid.notblank" => $this->translate->_("fishingsector_boatid_error"),
       									"nettype.notblank" => $this->translate->_("fishingsector_nettype_error"),
       									"hooksize.notblank" => $this->translate->_("fishingsector_hooksize_error"),
       									"hookno.notblank" => $this->translate->_("fishingsector_hookno_error")
       	       						)); 
	}
	/**
	 * Model relationships
	 */
	public function setUp() {
		parent::setUp(); 
		$this->hasOne('FishCatchSummary as fishcatchsummary',
							array('local' => 'catchsummaryid',
									'foreign' => 'id'
							)
						);	
		$this->hasOne('Boat as boat',
							array('local' => 'boatid',
									'foreign' => 'id'
							)
						);
		$this->hasOne('Commodity as mputa',
							array('local' => 'mputaid',
									'foreign' => 'id'
							)
						);	
		$this->hasOne('Commodity as ngege',
							array('local' => 'ngegeid',
									'foreign' => 'id'
							)
						);	
		$this->hasOne('Commodity as mukene',
							array('local' => 'mukeneid',
									'foreign' => 'id'
							)
						);	
		$this->hasOne('Commodity as mamba',
							array('local' => 'mambaid',
									'foreign' => 'id'
							)
						);
		$this->hasOne('Commodity as male',
							array('local' => 'maleid',
									'foreign' => 'id'
							)
						);
		$this->hasOne('Commodity as othertilapia',
							array('local' => 'othertilapiaid',
									'foreign' => 'id'
							)
						);
		$this->hasOne('Commodity as other',
							array('local' => 'otherid',
									'foreign' => 'id'
							)
						);			
	}
	
	/**
	 * Preprocess model data
	 */
	function processPost($formvalues){
		// Custom processing for integer and enum fields
		parent::processPost($formvalues);
	}
	/**
	 * Return the total weight of all the fish
	 */
	function getTotalWeight() {
		return $this->getMputaWeight() + $this->getNgegeWeight() + $this->getMukeneWeight() + $this->getMambaWeight() + $this->getMaleWeight() + $this->getOtherTilapiaWeight() + $this->getOtherWeight();
	}
}