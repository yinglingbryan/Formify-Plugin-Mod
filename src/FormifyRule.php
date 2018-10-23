<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_OPTIONS','FormifyOptions');
define('TABLE_FORMIFY_RECORDS','FormifyRecords');
define('TABLE_FORMIFY_ANSWERS','FormifyAnswers');
define('TABLE_FORMIFY_RULES','FormifyRules');

use \Concrete\Package\Formify\Src\FormifyForm;	
use \Concrete\Package\Formify\Src\FormifyField;	
use \Concrete\Package\Formify\Src\FormifyFieldType;	
use Loader;
use Package;
use Log;

class FormifyRule {
	
	private $assignableProperties = array(
		'fID',
		'ffID',
		'comparison',
		'value',
		'action',
		'actionFieldID',
		'ogID'
	);
	
	public function get($rID) {
		$db = Loader::db();
		$r = new self;
		
		$rData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_RULES . " WHERE rID = ?",array($rID));
		
		if (($rData['rID'] == $rID) && ($rID != 0)) {
			foreach($rData as $col => $val) {
				if($val != '') {
					$r->$col = $val;
				}
			}
			return $r;
		} else {
			return false;
		}
	}
	
	public function all($fID) {
		$db = Loader::db();
		$rData = $db->getAll("SELECT rID FROM " . TABLE_FORMIFY_RULES . " WHERE fID = ?",array($fID));
		$rules = array();
		if(count($rData) > 0) {
			foreach($rData as $rRow) {
				$rules[] = self::get($rRow['rID']);
			}
		}
		return $rules;
	}
	
	public function create($fID) {
		$db = Loader::db();
		$db->execute("INSERT INTO " . TABLE_FORMIFY_RULES . " (rID) VALUES (0)");
		$rID = $db->Insert_ID();
		$r = self::get($rID);
		$r->set('fID',$fID);
		return $r;
	}
	
	public function delete() {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_RULES . " WHERE rID=?",array($this->rID));
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
			$db->replace(TABLE_FORMIFY_RULES,array('rID'=>$this->rID,$property=>$value),'rID');
		}
		$this->$property = $value;
	}
		
}