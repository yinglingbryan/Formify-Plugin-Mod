<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_OPTIONS','FormifyOptions');
define('TABLE_FORMIFY_ANSWERS','FormifyAnswers');
define('TABLE_FORMIFY_RULES','FormifyRules');
define('TABLE_FORMIFY_INTEGRATIONS','FormifyIntegrations');
define('TABLE_FORMIFY_INTEGRATION_CONFIG','FormifyIntegrationConfig');

use \Concrete\Package\Formify\Src\FormifyFieldType;	
use \Concrete\Package\Formify\Src\FormifyOptionGroup;
use \Concrete\Package\Formify\Src\FormifyRecordSet;	
use Loader;
use Package;
use User;
use UserInfo;
use Log;

class FormifyField {
	
	private $assignableProperties = array(
		'label',
		'type',
		'sortPriority',
		'isPrimary',
		'isRequired',
		'isUnique',
		'isIndexable',
		'includeInEmail',
		'handle',
		'placeholder',
		'defaultValue',
		'defaultValueSource',
		'urlParameter',
		'fieldClass',
		'containerClass',
		'optionsSource',
		'ogFormID',
		'ogFieldID',
		'ogID',
		'firstOptionBlank',
		'fieldSize',
		'regex',
		'maxlength',
		'wysiwygFormat',
		'toolbar',
		'price',
		'qtyStart',
		'qtyEnd',
		'qtyIncrement',
		'commerceName',
		'dateFormat',
		'dateInterface',
		'minYear',
		'maxYear',
		'isExpiration',
		'timeInterval',
		'fsID',
		'nlToBr',
		'validateSection',
		'enableRules',
		'ruleAction',
		'ruleRequirement'
	);
	
	public function get($id,$fID=0) {
		if(is_array($id)) {
			$ff = self::getFromArray($id);
		} elseif(is_numeric($id)) {
			$ff = self::getByID($id,$fID);
		} else {
			$ff = self::getByHandle($id,$fID);
		}
		
		return $ff;
	}

	public function getByID($ffID,$fID=0) {
		$db = Loader::db();
		$field = new self;
		
		if($fID != 0) {
			$fieldData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_FIELDS . " WHERE ffID = ? AND fID = ?",array($ffID,$fID));
		} else {
			$fieldData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_FIELDS . " WHERE ffID = ?",array($ffID));
		}
		
		if (($fieldData['ffID'] == $ffID) && ($ffID != 0)) {
			foreach($fieldData as $col => $val) {
				if($val != '') {
					//Fix UTF-8 issues
					$field->$col = utf8_encode($val);
				}
			}
			
			$field->name = $field->ffID;
			$field->shortLabel = self::shortenText(strip_tags($field->label),25);
			
			if($fieldData['maxLength'] == 0) {
				$field->maxLength = '';
			}
		
			$field->getOptions();
			$field->getOptionsValues();
			
			if($field->isRequired) {
				$formInfo = $field->getFormInfo();
				$field->requiredIndicator = '<span style="color:' . $formInfo['requiredColor'] . '">' . $formInfo['requiredIndicator'] . '</span>';
			} else {
				$field->requiredIndicator = '';
			}
			
			switch($field->defaultValueSource) {
				case 'username':
					$u = new User();
					if($u->isLoggedIn()) {
						$field->defaultValue = $u->getUserName();
					}
					break;
				case 'email':
					$u = new User();
					if($u->isLoggedIn()) {
						$ui = UserInfo::getByID($u->getUserID());
						$field->defaultValue = $ui->getUserEmail();
					}
					break;
				case 'uID':
					$u = new User();
					$field->defaultValue = $u->getUserID();
					break;
				case 'url':
					$u = new User();
					$field->defaultValue = $_GET[$field->urlParameter];
					break;
				default:
					if($field->defaultValueSource != 'static') {
						$u = new User();
						if(($u->isLoggedIn()) && ($field->defaultValueSource != '')) {
							$ui = UserInfo::getByID($u->getUserID());
							$field->defaultValue = $ui->getAttribute($field->defaultValueSource);
						}
					}
			}
			
			$field->getRules();
			$field->getIntegrations();
			
			if(count($field->integrationConfig) == 0) {
				$field->integrationConfig = new \StdClass;
			}

			return $field;
			
		} else {
			return false;
		}
	}
	
	public function getByHandle($handle,$fID=0) {
		$db = Loader::db();
		if($fID != 0) {
			$row = $db->getRow("SELECT ffID FROM " . TABLE_FORMIFY_FIELDS . " WHERE handle = ? AND fID = ?",array($handle,$fID));
		} else {
			$row = $db->getRow("SELECT ffID FROM " . TABLE_FORMIFY_FIELDS . " WHERE handle = ?",array($handle));
		}
		if(intval($row['ffID']) != 0) {
			return self::getByID($row['ffID']);
		} else {
			return false;
		}
	}
	
	public function getFromArray($data) {
		$field = new self;
		$field->ffID = 0;
		$field->type = $data['type'];
		$field->label = $data['label'];
		$field->name = $data['name'];
		$field->required = $data['required'];
		$field->placeholder = $data['placeholder'];
		$field->options = $data['options'];
		
		return $field;
	}
	
	public function create($fID) {
		$db = Loader::db();
		
		$db->execute("INSERT INTO " . TABLE_FORMIFY_FIELDS . " (ffID,fID) VALUES (0,?)",array($fID));
		
		$ffID = $db->Insert_ID();
		
		$ff = self::get($ffID);
		
		$txt = Loader::helper('text');
		$label = 'Field ' . $ffID;
		$handle = $txt->handle($label);
		
		$ff->set('label',$label);
		$ff->set('handle',$handle);
		$ff->set('type','textbox');
		$ff->set('includeInEmail',1);
		$ff->set('optionsSource','static');
		
		return $ff;
	}
	
	public function propertyIsAssignable($property) {
		foreach($this->assignableProperties as $ap) {
			if($ap == $property) {
				return true;
			}	
		}
		return false;
	}
	
	public function set($property,$value) {
		$db = Loader::db();
		if(($this->propertyIsAssignable($property)) && ($value !== false)) {
			$db->replace(TABLE_FORMIFY_FIELDS,array('ffID'=>$this->ffID,$property=>$value),'ffID');
		}
		$this->$property = $value;
	}
	
	public function duplicate($fID='') {
		$originalField = self::getByID($this->ffID);
		$originalOptions = $originalField->getOptionsValues();
		
		if($fID == '') {
			$data['fID'] = $originalField->fID;
		} else {
			$data['fID'] = $fID;
		}
		
		$data['label'] = $originalField->label;
		$data['handle'] = $originalField->handle;
		$data['text'] = $originalField->text;
		$data['type'] = $originalField->type;
		$data['defaultValue'] = $originalField->defaultValue;
		$data['placeholder'] = $originalField->placeholder;
		$data['width'] = $originalField->width;
		$data['height'] = $originalField->height;
		$data['maxLength'] = $originalField->maxLength;
		$data['layout'] = $originalField->layout;
		$data['format'] = $originalField->format;
		$data['toolbar'] = $originalField->toolbar;
		$data['required'] = $originalField->required;
		$data['firstOptionBlank'] = $originalField->firstOptionBlank;
		$data['price'] = $originalField->price;
		$data['qtyStart'] = $originalField->qtyStart;
		$data['qtyEnd'] = $originalField->qtyEnd;
		$data['qtyIncrement'] = $originalField->qtyIncrement;
		$data['eCommerceName'] = $originalField->eCommerceName;
		$data['isExpirationField'] = $originalField->isExpirationField;
		$data['dateFormat'] = $originalField->dateFormat;
		$data['indexable'] = $originalField->indexable;
		$data['requireUnique'] = $originalField->requireUnique;
		$data['urlParameter'] = $originalField->urlParameter;
		$data['populateWith'] = $originalField->populateWith;
		$data['cssClass'] = $originalField->cssClass;
		$data['containerCssClass'] = $originalField->containerCssClass;
		$data['minYear'] = $originalField->minYear;
		$data['maxYear'] = $originalField->maxYear;
		$data['validateSection'] = $originalField->validateSection;
		$data['regex'] = $originalField->regex;
		$data['nlToBr'] = $originalField->nlToBr;
		$data['fsID'] = $originalField->fsID;
		
		$newField = self::create($data);
		$newField->setOptions($originalOptions);
		
		return $newField;
	}
	
	public function delete() {
		$db = Loader::db();
		$db->execute("UPDATE " . TABLE_FORMIFY_FIELDS . " SET isDeleted=1 WHERE ffID=?",array($this->ffID));
	}
	
	public function getType() {
		return \Concrete\Package\Formify\Src\FormifyFieldType::get($this->type);
	}
	
	public function getForm() {
		if(!$this->f) {
			$this->f = \Concrete\Package\Formify\Src\FormifyForm::get($this->fID);
		}
		return $this->f;
	}
	
	public function getFormInfo() {
		if(!$this->fInfo) {
			$this->fInfo = \Concrete\Package\Formify\Src\FormifyForm::getInfo($this->fID);
		}
		return $this->fInfo;
	}
	
	public function getOptions() {
		if(!$this->options) {
			$db = Loader::db();
			$options = array();
			
			switch($this->optionsSource) {
				case 'static': // Specify manually
					$options = $db->getAll("SELECT value as value, value as label FROM " . TABLE_FORMIFY_OPTIONS . " WHERE ffID = ? ORDER BY oID ASC",array($this->ffID));
					break;
				case 'optionGroup': // Option group
					$og = \Concrete\Package\Formify\Src\FormifyOptionGroup::get($this->ogID);
					$options = $og->options;
					break;
				case 'formRecords': // Form records
					if(intval($this->ogFormID) > 0) {
						$rs = \Concrete\Package\Formify\Src\FormifyRecordSet::get($this->ogFormID);
						$rs->setPageSize(0);
						$records = $rs->getRecords();
						foreach($records as $r) {
							$options[] = array('label' => $r->getAnswerValue($this->ogFieldID), 'value' => $r->getAnswerValue($this->ogFieldID));
						}
					}
					break;
			}
			
			$this->options = $options;	
		}
		
		return $this->options;
	}
	
	public function hasOptions() {
		if($this->getType()->hasOptions()) {
			return true;
		} else {
			return false;
		}
	}
	
	public function getOptionsValues() {
		if(!$this->optionsValues) {
			$options = $this->getOptions();
			$values = array();
			if(count($options) > 0) {	
				foreach($options as $o) {
					$values[] = $o['value'];
				}
			}
			
			$this->optionsValues = $values;
		}
		return $this->optionsValues;
	}
	
	public function setOptions($options) {
    	$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_OPTIONS . " WHERE ffID=?",array($this->ffID));
		if(count($options) > 0) {
			foreach($options as $option) {
				$option = trim($option);
				if ($option != '') {
					$db->execute("INSERT INTO " . TABLE_FORMIFY_OPTIONS . " (oID,ffID,value) VALUES (0,?,?)",array($this->ffID,$option));
				}
			}
		}
	}
	
	public function setRules($rules) {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_RULES . " WHERE ffID=?",array($this->ffID));
		if(count($rules) > 0) {
			foreach($rules as $rule) {
				if(($rule->comparisonField->ffID != '') && ($rule->comparison != '') && ($rule->value != '')) {
					$db->execute("INSERT INTO " . TABLE_FORMIFY_RULES . " (rID,ffID,comparisonFieldID,comparison,value) VALUES (0,?,?,?,?)",array($this->ffID,$rule->comparisonField->ffID,$rule->comparison,$rule->value));
				}
			}
		}
	}
	
	public function shortenText($strString, $nLength = 15, $strTrailing = "...") {
		$nLength -= strlen($strTrailing);
		if (strlen($strString) > $nLength) {
			return substr($strString, 0, $nLength) . $strTrailing;
		} else {
			return $strString;
		}
	}
	
	public function render() {
		$this->getType()->render($this);
	}
	
	public function validateUniqueness($value) {
		$db = Loader::db();
		$row = $db->getRow("SELECT asID FROM " . TABLE_FORMIFY_ANSWERS . " WHERE ffID = ? AND value = ?",array($this->ffID,$value));
		if(intval($row['asID']) != 0) {
			if(\Concrete\Package\Formify\Record::getByID($row['asID'])) {
				//Duplicate answer exists, so don't validate
				return false;
			} else {
				//Answer is unique
				return true;
			}
		}
		return true;
	}
	
	public function getRules() {
		if(!$this->rules) {
			if($this->enableRules) {
				$db = Loader::db();
				$rules = $db->getAll("SELECT * FROM " . TABLE_FORMIFY_RULES . " WHERE ffID=? ORDER BY rID",array($this->ffID));
				$this->rules = $rules;
			} else {
				$this->rules = array();
			}
		}
		return $this->rules;
	}
	
	public function isRuleActionField() {
		$db = Loader::db();
		$rules = $db->getAll("SELECT actionField FROM " . TABLE_FORMIFY_RULES . " WHERE actionField = ?",array($this->ffID));
		if(count($rules) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function isDefaultValue($value) {
		if($value != '') {
			if(is_array($this->defaultValue)) {
				foreach($this->defaultValue as $dv) {
					if($dv == $value) {
						return true;
					}
				}
			} else {
				if($this->defaultValue == $value) {
					return true;
				}
			}
		}
		return false;
	}
	
	public function getIntegrations() {
		if(!$this->integrations) {
			if($f = $this->getForm()) {
				$integrations = $f->getIntegrations(true);
				
				if(count($integrations) > 0) {
					foreach($integrations as $key => $i) {
						$this->getIntegrationConfig($i->handle);
					}
				}
				
				$this->integrations = $integrations;
			}
			
		}
		
		return $this->integrations;
	}
	
	
	public function checkIntegration($handle) {
		$f = $this->getForm();
		return $f->checkIntegration($handle);
	}
	
	public function setIntegrationConfig($handle,$key,$value) {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_INTEGRATION_CONFIG . " WHERE ffID = ? AND handle = ? AND configKey = ?",array($this->ffID,$handle,$key));
		$db->execute("INSERT INTO " . TABLE_FORMIFY_INTEGRATION_CONFIG . " (icID, ffID, handle, configKey, configValue) VALUES (0,?,?,?,?)",array($this->ffID,$handle,$key,$value));
	}
	
	public function getIntegrationConfig($handle,$key='') {
		$db = Loader::db();
		
		$this->integrationConfig = array();
		
		if($key != '') {
			$configData = $db->getRow("SELECT configValue FROM " . TABLE_FORMIFY_INTEGRATION_CONFIG . " WHERE ffID = ? AND handle = ? AND configKey = ?",array($this->ffID,$handle,$key));
			$this->integrationConfig[$handle][$key] = $configData['configValue'];
			return $this->integrationConfig[$handle][$key];
		} else {
			$configData = $db->getAll("SELECT configKey, configValue FROM " . TABLE_FORMIFY_INTEGRATION_CONFIG . " WHERE ffID = ? AND handle = ?",array($this->ffID,$handle));
			$config = array();
			foreach($configData as $cd) {
				$this->integrationConfig[$handle][$cd['configKey']] = $cd['configValue'];
			}
			
			return $this->integrationConfig[$handle];
		}
		
	}
	
	public function validate($data) {
		
		$errors = array();
		
		/* Required fields */
		if(($this->isRequired) && ($this->getType()->hasInput())) { //Determine if it's required
    		if(is_array($data)) {
        		if(count($data) > 0) {
            		foreach($data as $value) {
                		if($value == '') {
                    		return false;
                		}
            		}
        		}
    		} else {
    			if($data == '') { //See if the actual value is blank
    				return false;
    			}
			}
		}
		
		/* Regular expression validation */
		if($this->regex != '') {
			if($data != '') {
				if((preg_match($this->regex,$data) == 0) && ($data != '')) {
					return false;
				}
			}
		}
		
		/* Email validation */
		if($this->type == 'email') {
			if((!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", $data)) && ($data != '')) {
				return false;
			}
		}
		
		if($this->type == 'date') {
    		if(is_array($data)) { // Date value is in array parts
        		$dateString = $data[0] . '-' . $data[1] . '-' . $data[2];
				$dt = \DateTime::createFromFormat('n-j-Y',$dateString);
			} else { // Date value is string
				if($field->dateFormat != '') {
    				$df = $this->dateFormat;
				} else {
    				$df = 'F j, Y';
				}
				$dateString = $data;
				$dt = \DateTime::createFromFormat($this->dateFormat,$dateString);
			}
			
			$dateErrors = \DateTime::getLastErrors();
			if(($dateErrors['warning_count'] > 0) || ($dateErrors['error_count'] > 0)) {
    			if(($dateString != '--') && ($dateString != '')) { 
        			return false;
                }
			}
		}
		
		return true;
		
	}
	
}
?>