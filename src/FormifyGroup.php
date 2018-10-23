<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_GROUPS','FormifyGroups');
define('TABLE_FORMIFY_FORMS','FormifyForms');

use \Concrete\Package\Formify\Src\FormifyForm;	
use \Concrete\Package\Formify\Src\FormifyField;	
use \Concrete\Package\Formify\Src\FormifyFieldType;	
use Loader;
use Package;
use Log;

class FormifyGroup {
	
	private $assignableProperties = array(
		'name',
		'sortPriority'
	);
	
	public function get($gID) {
		$db = Loader::db();
		$g = new self;
		
		$gData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_GROUPS . " WHERE gID = ?",array($gID));
		
		if (($gData['gID'] == $gID) && ($gID != 0)) {
			foreach($gData as $col => $val) {
				if($val != '') {
					$g->$col = $val;
				}
			}
			return $g;
		} else {
			return false;
		}
	}
	
	public function all() {
		$db = Loader::db();
		$gData = $db->getAll("SELECT gID FROM " . TABLE_FORMIFY_GROUPS . " ORDER BY sortPriority ASC, gID DESC");
		$groups = array();
		
		/*
		$g = new self;
		$g->name = t('All Forms');
		$g->sortPriority = 0;
		
		$groups[] = $g;
		*/
		
		if(count($gData) > 0) {
			foreach($gData as $gRow) {
				$groups[] = self::get($gRow['gID']);
			}
		}
		return $groups;
	}
	
	public function create($name) {
		$db = Loader::db();
		$db->execute("INSERT INTO " . TABLE_FORMIFY_GROUPS . " (gID) VALUES (0)");
		$gID = $db->Insert_ID();
		$g = self::get($gID);
		$g->set('name',$name);
		return $g;
	}
	
	public function delete() {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_GROUPS . " WHERE gID=?",array($this->gID));
		$db->execute("UPDATE " . TABLE_FORMIFY_FORMS . " SET gID=0 WHERE gID=?",array($this->gID));
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
			$db->replace(TABLE_FORMIFY_GROUPS,array('gID'=>$this->gID,$property=>$value),'gID');
		}
		$this->$property = $value;
	}
	
}