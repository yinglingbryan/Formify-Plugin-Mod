<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FORMS','FormifyForms');
define('TABLE_FORMIFY_PERMISSIONS','FormifyPermissions');
define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_NOTIFICATIONS','FormifyNotifications');
define('TABLE_FORMIFY_RECORDS','FormifyRecords');
define('TABLE_FORMIFY_INTEGRATIONS','FormifyIntegrations');
define('TABLE_FORMIFY_INTEGRATION_CONFIG','FormifyIntegrationConfig');
define('TABLE_FORMIFY_MIGRATIONS','FormifyMigrations');

use \Concrete\Package\Formify\Src\FormifyObject;	
use \Concrete\Package\Formify\Src\FormifyField;	
use \Concrete\Package\Formify\Src\FormifyNotification;
use \Concrete\Package\Formify\Src\FormifyFormSection;
use \Concrete\Package\Formify\Src\FormifyRecordSet;
use Loader;
use Log;
use User;
use Package;

class FormifyForm {
	
	private $assignableProperties = array(
		'dateCreated',
		'name',
		'handle',
		'submitAction',
		'submitActionCollectionID',
		'submitActionCollectionName',
		'submitActionMessage',
		'submitActionURL',
		'submitActionPassRecordID',
		'submitActionRecordIDParameter',
		'submitActionProcessor',
		'submitLabel',
		'maxSubmissions',
		'commerceGateway',
		'commerceCurrencySymbol',
		'commerceConfirmationMessage',
		'commerceMaximumOrderPrice',
		'captcha',
		'grecaptcha',
		'grecaptchaSiteKey',
		'grecaptchaSecretKey',
		'requiredIndicator',
		'requiredColor',
		'defaultRecordStatus',
		'disableCache',
		'ownerCanEdit',
		'ownerCanDelete',
		'oneRecordPerUser',
		'autoIndex',
		'magic',
		'indexTimestamp',
		'exportTimestamp',
		'autoExpire',
		'errorValidation',
		'errorSubmissions',
		'errorCaptcha',
		'errorEcommerce',
		'errorPermission',
		'showSections',
		'gID'
	);
	
	public $recordCount = 0;
	public $fieldCount = 0;
	
	public function get($id) {
		if(is_numeric($id)) {
			$f = self::getByID($id);
		} else {
			$f = self::getByHandle($id);
		}
		
		return $f;
	}
	
	public function create($name,$email='') {
		$db = Loader::db();
		
		$dateCreated = time();
		
		$txt = Loader::helper('text');
		$handle = $txt->handle($name);
		
		$db->execute("INSERT INTO " . TABLE_FORMIFY_FORMS . " (fID,dateCreated,name,handle) VALUES (0,?,?,?)",array($dateCreated,$name,$handle));
		$f =  self::getByID($db->Insert_ID());
		$f->setDefaults();
		$f->resetPermissions();
		return $f;
	}
	
	public function setDefaults() {
		$this->set('submitLabel',t('Submit'));
		$this->set('submitAction','message');
		$this->set('submitActionMessage',t('Your information has been submitted.'));
		$this->set('commerceCurrencySymbol','$');
		$this->set('requiredIndicator','*');
		$this->set('requiredColor','#ff0000');
		$this->set('gateway','paypal');
		$this->set('errorValidation',t('Please review the highlighted fields.'));
		$this->set('errorSubmissions',t('This form is no longer receiving submissions.'));
		$this->set('errorCaptcha',t('Incorrect validation code.  Please try again.'));
		$this->set('errorEcommerce',t('There was an error processing the transaction.'));
		$this->set('errorPermission',t('You do not have access to submit this form.'));
	}
	
	public function getByHandle($handle) {
		$db = Loader::db();
		$fData = $db->getRow("SELECT fID FROM " . TABLE_FORMIFY_FORMS . " WHERE handle = ? AND isDeleted != 1",array($handle));
		if(intval($fData['fID']) != 0) {
			return self::getByID($fData['fID']);
		} else {
			return false;
		}
	}
	
	public function getByID($fID) {
		$db = Loader::db();
		$f = new self;
		
		//Set properties
		$fData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_FORMS . " WHERE fID=? AND isDeleted != 1", array($fID));
		if (($fData['fID'] == $fID) && ($fID != 0)) {
			foreach($fData as $col => $val) {
				if($val != '') {
					$f->$col = $val;
				}
			}
			
			/*
			$f->magic = (bool) $f->magic;
			$f->captcha = (bool) $f->captcha;
			$f->submitActionPassRecordID = (bool) $f->submitActionPassRecordID;
			$f->ownerCanEdit = (bool) $f->ownerCanEdit;
			$f->ownerCanDelete = (bool) $f->ownerCanDelete;
			$f->oneRecordPerUser = (bool) $f->oneRecordPerUser;
			$f->recordCount = $f->getRecordCount();
			$f->permissions = $f->getPermissions();
			$f->fields = $f->getFields();
			$f->rules = $f->getRules();
			$f->integrations = $f->getIntegrations();
			$f->sections = $f->getSections();
			*/
			
			//$f->gID = (int) $f->gID;
			
			return $f;
		} else {
			return false;
		}
	}
	
	public function getInfo($fID) {
		$db = Loader::db();
		$fData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_FORMS . " WHERE fID=? AND isDeleted != 1", array($fID));
		return $fData;
	}
	
	public function getAll() {
		$db = Loader::db();
		$formsData = $db->getAll("SELECT fID FROM " . TABLE_FORMIFY_FORMS . " WHERE isDeleted != 1 ORDER BY name ASC");
		$forms = array();
		
		if(count($formsData) > 0) {
			foreach($formsData as $formRow) {
				$f = self::getByID($formRow['fID']);
				$f->getRecordCount();
				$f->getFieldCount();
				$forms[] = $f;
			}
		}
		return $forms;
	}
	
	public function getFormID() {
		return $this->fID;
	}
	
	public function getFormName() {
		return $this->name;
	}
	
	public function getFormHandle() {
		return $this->handle;	
	}
	
	public function propertyIsAssignable($property) {
		foreach($this->assignableProperties as $ap) {
			if($ap == $property) {
				return true;
			}	
		}
		return false;
	}

	/* UPDATED BY MGH FOR RECAPTCHA INTEGRATION	
	
	THIS WILL MAKE THE RECAPTCHA KEYS UPDATE FOR ALL FORMS AT ONCE

	public function set($property,$value) {
		$db = Loader::db();
		if(($this->propertyIsAssignable($property)) && ($value !== false)) {
			$db->replace(TABLE_FORMIFY_FORMS,array('fID'=>$this->fID,$property=>$value),'fID');
		}
		$this->$property = $value;
	}
	*/

	public function set($property,$value) {

		$db = Loader::db();
		$formsArr = $this->getAll();

		if(($this->propertyIsAssignable($property)) && ($value !== false)) {

			// test //
			if (($property == "grecaptchaSiteKey") || ($property == "grecaptchaSecretKey")) {				
				foreach($formsArr as $formID) {
					$db->replace(TABLE_FORMIFY_FORMS,array('fID'=>$formID->fID,$property=>$value),'fID');
					}
				} else {
					$db->replace(TABLE_FORMIFY_FORMS,array('fID'=>$this->fID,$property=>$value),'fID');
				}

		}

		if (($property == "grecaptchaSiteKey") || ($property == "grecaptchaSecretKey")) {
		foreach($formsArr as $formID) { $formID->$property = $value; }
		} else {
		$this->$property = $value;
		}
		
	}

	//// END UPDATE
	
	public function delete() {
		$db = Loader::db();
		$db->execute("UPDATE " . TABLE_FORMIFY_FORMS . " SET isDeleted=1 WHERE fID=?",array($this->fID));
	}
	
	public function duplicate() {
		
	}
	
	public function addField() {
		$field = \Concrete\Package\Formify\Src\FormifyField::create($this->fID);
		return $field;
	}
	
	public function getFields() {
		if(!$this->fields) {			
			$db = Loader::db();
			$fieldsData = $db->getAll("SELECT ffID FROM " . TABLE_FORMIFY_FIELDS . " WHERE fID = ? AND isDeleted != 1 ORDER by sortPriority ASC, ffID",array($this->fID));
			$fields = array();
			foreach($fieldsData as $fieldData) {
				$ff = \Concrete\Package\Formify\Src\FormifyField::getByID($fieldData['ffID']);
				$ff->getIntegrations();
				$fields[] = $ff;
			}
			$this->fields = $fields;
		}
		
		return $this->fields;
	}
	
	public function getField($id) {
		foreach($this->getFields() as $ff) {
			if($ff->ffID == $id) {
				return $ff;
			}
		}
		return \Concrete\Package\Formify\Src\FormifyField::get($id,$this->fID);
	}
	
	public function getFieldByHandle() {
		
	}
	
	public function addRecord() {
		return \Concrete\Package\Formify\Src\FormifyRecord::create($this);
	}
	
	public function getRecordCount() {
		if(intval($this->recordCount) == 0) {
			//Calculate Record Count
			$db = Loader::db();
			$count = reset($db->getCol("SELECT COUNT(rID) AS count FROM " . TABLE_FORMIFY_RECORDS . " WHERE fID = ? AND isDeleted != 1",array($this->fID)));
			$this->recordCount = $count;
		}
		
		return $this->recordCount;
	}
	
	public function getFieldCount() {
		if(intval($this->fieldCount) == 0) {
			//Calculate Field Count
			$db = Loader::db();
			$count = reset($db->getCol("SELECT COUNT(ffID) AS count FROM " . TABLE_FORMIFY_FIELDS . " WHERE fID = ? AND isDeleted != 1",array($this->fID)));
			$this->fieldCount = $count;
		}
		
		return $this->fieldCount;
	}
	
	public function getRecordCountByUser() {
		if($this->userRecordCount) {
			return $this->userRecordCount;
		} else {
			$db = Loader::db();
			$u = new User();
			$rCount = $db->getRow("SELECT count(rID) AS total FROM " . TABLE_FORMIFY_RECORDS . " WHERE fID=? AND uID=? AND isDeleted != 1",array($this->fID,intval($u->getUserID())));
			$this->userRecordCount = $rCount['total'];
			return $this->userRecordCount;
		}
	}
	
	public function getRules() {
		if(!$this->rules) {
			$rules = array();
			foreach($this->getFields() as $ff) {
				foreach($ff->getRules() as $r) { 
					$rules[] = $r;
				}
			}
			$this->rules = $rules;
		}
		return $this->rules;
	}
	
	public function getRecordSet() {
		return \Concrete\Package\Formify\Src\FormifyRecordSet::get($this->fID);
	}
	
	public function clearRecords() {
		
	}
	
	public function clearRecordCache() {
		
	}
	
	public function indexRecords() {
		
	}
	
	public function getNotifications($type='') {
		return \Concrete\Package\Formify\Src\FormifyNotification::getByFormID($this->fID,$type);
	}
	
	public function addNotification() {
		return \Concrete\Package\Formify\Src\FormifyNotification::create($this->fID);
	}
	
	public function clearNotifications() {
		
	}
	
	public function clearRules() {
		
	}
	
	public function getAttribute() {
		
	}
	
	public function getFormAttributeValue() {
		
	}
	
	public function clearAttributes() {
		
	}
	
	public function getAttributeValueObject() {
		
	}
	
	public function setAttribute() {
		
	}
	
	public function clearAttribute() {
		
	}
	
	public function addAttribute() {
		
	}
	
	public function hasSiteMapField() {
		
	}
	
	public function hasFileManagerField() {
		
	}
	
	public function hasFileUploadField() {
		
	}
	
	public function hasWYSIWYGField() {
		
	}
	
	public function hasDateField() {
		
	}
	
	public function hasCommerceField() {
		
	}
	
	public function getPaymentGateways() {
		
	}
	
	public function getPaymentGatewayPath() {
		
	}
	
	public function loadPaymentGatewayConfig() {
		
	}
	
	public function loadPaymentGatewayProcessor() {
		
	}
	
	public function getCustomProcessors() {
		
	}
	
	public function resetPermissions() {
		$db = Loader::db();
		
		$db->execute("DELETE FROM " . TABLE_FORMIFY_PERMISSIONS . " WHERE fID = ?",array($this->fID));
		
		//Give guests permission to add records - This query assumes guests are group ID 1
		$this->togglePermission('add',1);
		
		//Give administrators full permissions - This query assumes administrators are group ID 3
		$this->togglePermission('add',3);
		$this->togglePermission('edit',3);
		$this->togglePermission('delete',3);
		$this->togglePermission('approve',3);
	}
	
	public function getPermissions() {
		if(count($this->permissions) == 0) {
			$db = Loader::db();
			$permissions = array('add' => array(),'edit' => array(),'approve' => array(),'delete' => array());
			$pData = $db->getAll("SELECT * FROM " . TABLE_FORMIFY_PERMISSIONS . " WHERE fID = ?",array($this->fID));
			if(count($pData) > 0) {
				foreach($pData as $p) {
					$permissions[$p['type']][] = $p['gID'];
				}
			}
			$this->permissions = $permissions;
		}
		return $this->permissions;
	}
	
	public function checkPermission($type,$gID) {
		$db = Loader::db();
		$pData = $db->getRow("SELECT fpID FROM " . TABLE_FORMIFY_PERMISSIONS . " WHERE fID = ? AND type = ? AND gID = ?",array($this->fID,$type,$gID));
		if(count($pData) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function togglePermission($type,$gID) {
		$db = Loader::db();
		if($this->checkPermission($type,$gID)) {
			$db->execute("DELETE FROM " . TABLE_FORMIFY_PERMISSIONS . " WHERE fID = ? AND type = ? AND gID = ?",array($this->fID,$type,$gID));
		} else {
			$db->execute("INSERT INTO " . TABLE_FORMIFY_PERMISSIONS . " (fpID, fID, gID, type) VALUES (0,?,?,?)",array($this->fID,$gID,$type));
		}
	}
	
	public function userCanAdd() {
		$u = new User();
		
		//Check whether or not "One Record Per User" is set
		if($this->oneRecordPerUser) {
			if(intval($u->getUserID()) == 0) { //User is not logged in
				return false;
			} else {
				if($this->getRecordCountByUser() > 0) { //User has already submitted once
					return false;
				}
			}
			return true;
		} else { //If "One Record Per User" is not set, the only other limiting factor is standard permissions
			if($u->isSuperUser()) { //Super user can always add
				return true;
			}
			
			foreach($u->uGroups as $gID) { //Loop through the groups
				if($this->groupCanAdd($gID)) { //If user is part of a group that can add, they can add
					return true;
				}
			}
		}
		
		return false; //Deny access by default
	}
	
	public function userCanEdit() {
		$u = new User();
		if($u->isSuperUser()) { //Super user can always add
			return true;
		}
		
		foreach($u->uGroups as $gID => $gName) { //Loop through the groups
			if($this->groupCanEdit($gID)) { //If user is part of a group that can add, they can add
				return true;
			}
		}
		
		return false;
	}
	
	public function userCanApprove() {
		$u = new User();
		
		if($u->isSuperUser()) { //Super user can always approve
			return true;
		}
		
		foreach($u->uGroups as $gID => $gName) {
			if($this->groupCanApprove($gID)) {
				return true;
			}
		}
		
		return false; //Deny access by default
	}
	
	public function ownerCanEdit() {
		return (bool) $this->ownerCanEdit;
	}
	
	public function ownerCanDelete() {
		return (bool) $this->ownerCanDelete;
	}

	public function groupCanAdd($gID) {
		$db = Loader::db();
		$this->getPermissions();
		if(count($this->permissions['add'])) {
			foreach($this->permissions['add'] as $pgID) {
				if($pgID == $gID) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function groupCanEdit($gID) {
		$db = Loader::db();
		$this->getPermissions();
		if(count($this->permissions['edit'])) {
			foreach($this->permissions['edit'] as $pgID) {
				if($pgID == $gID) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function groupCanDelete($gID) {
		$db = Loader::db();
		$this->getPermissions();
		if(count($this->permissions['delete'])) {
			foreach($this->permissions['delete'] as $pgID) {
				if($pgID == $gID) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function groupCanApprove($gID) {
		$db = Loader::db();
		$this->getPermissions();
		if(count($this->permissions['approve'])) {
			foreach($this->permissions['approve'] as $pgID) {
				if($pgID == $gID) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function isMagic() {
		return $this->magic;
	}
	
	public function getIntegrations($requireActive = false) {
		if((!$this->integrations) || ($requireActive)) {
			
			$allIntegrations = \Concrete\Package\Formify\Src\FormifyIntegration\Integration::all();	
				
			$integrations = array();		
			
			foreach($allIntegrations as $i) {
				if($this->checkIntegration($i->handle)) {
					$i->active = 1;
					$i->config = $this->getIntegrationConfig($i->handle);
				} else {
					$i->active = 0;
				}
					
				if($requireActive) {
					if($i->active == 1) {
						$integrations[] = $i;
					}
				} else {
					$integrations[] = $i;
				}
			}
			
			$this->integrations = $integrations;
			
		}
		
		return $this->integrations;
	}
	
	public function getActiveIntegrations() {
		return $this->getIntegrations(true);
	}
	
	
	public function checkIntegration($handle) {
		$db = Loader::db();
		$iData = $db->getRow("SELECT iID FROM " . TABLE_FORMIFY_INTEGRATIONS . " WHERE fID = ? AND handle = ?",array($this->fID,$handle));
		if(count($iData) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function toggleIntegration($handle) {
		$db = Loader::db();
		if($this->checkIntegration($handle)) {
			$this->deactivateIntegration($handle);
		} else {
			$this->activateIntegration($handle);
		}
	}
	
	public function activateIntegration($handle) {
		$db = Loader::db();
		$db->execute("INSERT INTO " . TABLE_FORMIFY_INTEGRATIONS . " (iID, fID, handle) VALUES (0,?,?)",array($this->fID,$handle));
	}
	
	public function deactivateIntegration($handle) {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_INTEGRATIONS . " WHERE fID = ? AND handle = ?",array($this->fID,$handle));
	}
	
	public function setIntegrationConfig($handle,$key,$value) {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_INTEGRATION_CONFIG . " WHERE fID = ? AND handle = ? AND configKey = ?",array($this->fID,$handle,$key));
		$db->execute("INSERT INTO " . TABLE_FORMIFY_INTEGRATION_CONFIG . " (icID, fID, handle, configKey, configValue) VALUES (0,?,?,?,?)",array($this->fID,$handle,$key,$value));
	}
	
	public function getIntegrationConfig($handle,$key='') {
		$db = Loader::db();
		
		if($key != '') {
			$configData = $db->getRow("SELECT configValue FROM " . TABLE_FORMIFY_INTEGRATION_CONFIG . " WHERE fID = ? AND handle = ? AND configKey = ?",array($this->fID,$handle,$key));
			return $configData['configValue'];
		} else {
			$configData = $db->getAll("SELECT configKey, configValue FROM " . TABLE_FORMIFY_INTEGRATION_CONFIG . " WHERE fID = ? AND handle = ?",array($this->fID,$handle));
			$config = array();
			foreach($configData as $cd) {
				$config[$cd['configKey']] = $cd['configValue'];
			}
			
			return $config;
		}
		
	}
	
	public function getSections() {
		if(!$this->sections) {			
			$sections = array();

			$i = 1;			
			$s = new FormifyFormSection;
			$s->index = $i;
			
			foreach($this->getFields() as $ff) {
				if($ff->type == 'divider') {
					$sections[] = $s;
					
					$i++;
					$s = new FormifyFormSection;
					$s->index = $i;
					$s->validate = $ff->validateSection;
				} else {
					$s->addField($ff);
				}
			}
			
			$sections[] = $s;
			
			$this->sections = $sections;
		}
		return $this->sections;
	}
	
	public function getSection($index) {
		foreach($this->getSections() as $s) {
			if($s->index ==  $index) {
				return $s;
			}
		}
	}
	
	public function logMigration() {
		$db = Loader::db();
		$db->execute("INSERT INTO " . TABLE_FORMIFY_MIGRATIONS . " (mID,fID,dateMigrated) VALUES (0,?,?)",array($this->fID,time()));
	}
	
	public function isMigrated() {
		$db = Loader::db();
		$mData = $db->getAll("SELECT * FROM " . TABLE_FORMIFY_MIGRATIONS . " WHERE fID = ?",array($this->fID));
		if(count($mData) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
}