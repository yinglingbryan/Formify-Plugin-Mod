<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_OPTIONS','FormifyOptions');
define('TABLE_FORMIFY_RECORDS','FormifyRecords');
define('TABLE_FORMIFY_ANSWERS','FormifyAnswers');
define('TABLE_FORMIFY_RULES','FormifyRules');
define('TABLE_FORMIFY_OPTION_GROUPS','FormifyOptionGroups');
define('TABLE_FORMIFY_OPTION_GROUP_OPTIONS','FormifyOptionGroupOptions');

use \Concrete\Package\Formify\Src\FormifyForm;	
use \Concrete\Package\Formify\Src\FormifyField;	
use \Concrete\Package\Formify\Src\FormifyFieldType;	
use Loader;
use Package;
use Log;

class FormifyOptionGroup {
	
	private $assignableProperties = array(
		'name'
	);
	
	public function get($ogID) {
		$db = Loader::db();
		$og = new self;
		
		$ogData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_OPTION_GROUPS . " WHERE ogID = ?",array($ogID));
		
		if (($ogData['ogID'] == $ogID) && ($ogID != 0)) {
			foreach($ogData as $col => $val) {
				if($val != '') {
					$og->$col = $val;
				}
			}
			$og->getOptions();
			return $og;
		} else {
			return false;
		}
	}
	
	public function all() {
		$db = Loader::db();
		$ogData = $db->getAll("SELECT ogID FROM " . TABLE_FORMIFY_OPTION_GROUPS . " ORDER BY ogID DESC");
		$optionGroups = array();
		if(count($ogData) > 0) {
			foreach($ogData as $ogRow) {
				$optionGroups[] = self::get($ogRow['ogID']);
			}
		}
		return $optionGroups;
	}
	
	public function create($name) {
		$db = Loader::db();
		$db->execute("INSERT INTO " . TABLE_FORMIFY_OPTION_GROUPS . " (ogID) VALUES (0)");
		$ogID = $db->Insert_ID();
		$og = self::get($ogID);
		$og->set('name',$name);
		return $og;
	}
	
	public function delete() {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_OPTION_GROUPS . " WHERE ogID=?",array($this->ogID));
		$db->execute("DELETE FROM " . TABLE_FORMIFY_OPTION_GROUP_OPTIONS . " WHERE ogID=?",array($this->ogID));
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
		if($this->propertyIsAssignable($property)) {
			$db->replace(TABLE_FORMIFY_OPTION_GROUPS,array('ogID'=>$this->ogID,$property=>$value),'ogID');
		}
		$this->$property = $value;
	}
	
	public function setOptions($options) {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_OPTION_GROUP_OPTIONS . " WHERE ogID=?",array($this->ogID));
		foreach($options as $option) {
			if(is_array($option)) {
				$label = trim($option['label']);
				$value = trim($option['value']);
				if ($option != '') {
					$db->execute("INSERT INTO " . TABLE_FORMIFY_OPTION_GROUP_OPTIONS . " (oID,ogID,label,value) VALUES (0,?,?,?)",array($this->ogID,$label,$value));
				}
			} else {
				$value = trim($option);
				if ($option != '') {
					$db->execute("INSERT INTO " . TABLE_FORMIFY_OPTION_GROUP_OPTIONS . " (oID,ogID,label,value) VALUES (0,?,?,?)",array($this->ogID,$value,$value));
				}
			}
		}
	}
	
	public function getOptions() {
		if(count($this->options) > 0) {
			return $this->options;
		} else {
			$db = Loader::db();
			$options = array();
			$optionsValues = array();
			$optionsData = $db->getAll("SELECT * FROM " . TABLE_FORMIFY_OPTION_GROUP_OPTIONS . " WHERE ogID=?",array($this->ogID));
			foreach($optionsData as $oData) {
				$o = array();
				$o['label'] = $oData['label'];
				$o['value'] = $oData['value'];
				$options[] = $o;
				$optionsValues[] = $oData['value'];
			}
			$this->options = $options;
			$this->optionsValues = $optionsValues;
			return $options;
		}
	}
	
}