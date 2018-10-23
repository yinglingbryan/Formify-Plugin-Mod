<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_OPTIONS','FormifyOptions');
define('TABLE_FORMIFY_RECORDS','FormifyRecords');
define('TABLE_FORMIFY_ANSWERS','FormifyAnswers');
define('TABLE_FORMIFY_NOTIFICATIONS','FormifyNotifications');
define('TABLE_FORMIFY_RULES','FormifyRules');

use \Concrete\Package\Formify\Src\FormifyForm;	
use \Concrete\Package\Formify\Src\FormifyField;	
use \Concrete\Package\Formify\Src\FormifyFieldType;	
use \Concrete\Package\Formify\Src\FormifyTemplate;	
use Loader;
use Package;
use Log;
use Config;
use User;
use UserInfo;

class FormifyNotification {
	
	private $assignableProperties = array(
		'type',
		'fromName',
		'replyAddress',
		'toAddress',
		'subject',
		'tID',
		'conditionFieldID',
		'conditionType',
		'conditionValue'
	);
	
	public function get($nID) {
		$db = Loader::db();
		$n = new self;
		$nData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_NOTIFICATIONS . " WHERE nID = ?",array($nID));
		if(($nData['nID'] == $nID) && ($nData['nID'] != 0)) {
			foreach($nData as $col => $val) {
				if($val != '') {
					$n->$col = $val;
				}
			}
			return $n;
		} else {
			return false;
		}
	}
	
	public function getByFormID($fID,$type='') {
		$db = Loader::db();
		$notifications = array();
		if($type != '') {
			$nRows = $db->getAll("SELECT nID FROM " . TABLE_FORMIFY_NOTIFICATIONS . " WHERE fID = ? AND type = ?",array($fID,$type));
		} else {
			$nRows = $db->getAll("SELECT nID FROM " . TABLE_FORMIFY_NOTIFICATIONS . " WHERE fID = ?",array($fID));
		}
		if(count($nRows) > 0) {
			foreach($nRows as $n) {
				$notifications[] = self::get($n['nID']);
			}
		}
		return $notifications;
	}
	
	public function create($fID) {
		$db = Loader::db();
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$fromName = Config::get('concrete.site');
		$subject = $f->name . ' Notification';
		$u = new User();
		$ui = UserInfo::getByID($u->getUserID());
		$toAddress = $ui->getUserEmail();
		$db->execute("INSERT INTO " . TABLE_FORMIFY_NOTIFICATIONS . " (nID,fID,fromName,toAddress,subject) VALUES (0,?,?,?,?)",array($fID,$fromName,$toAddress,$subject));
		$n = self::get($db->Insert_ID());
		
		return $n;
	}
	
	public function delete() {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_NOTIFICATIONS . " WHERE NID = ? LIMIT 1",array($this->nID));
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
			$db->replace(TABLE_FORMIFY_NOTIFICATIONS,array('nID'=>$this->nID,$property=>$value),'nID');
		}
		$this->$property = $value;
	}
	
	public function send($record) {
		
		$this->record = $record;
		
		$fromName= $this->fromName;
		$to = $this->getDestination();
		$reply = $this->getReply();
		$subject = $this->subject;
		
		$t = \Concrete\Package\Formify\Src\FormifyTemplate::get($this->tID);
		$html = $t->render($record,true);
		
		if(($to != '') && ($subject != '')) {
			
			if($this->verifyCondition()) {
				$mh = Loader::helper('mail');
				$mh->to($to);
				$mh->from('noreply@concrete5.org',$fromName);
				if($reply != '') {
					$mh->replyto($reply);
				}
				$mh->setSubject($subject);
				$mh->setBodyHTML($html);
				@$mh->sendMail();
			}
			
		}
		
	}
	
	public function getDestination() {
		
		$record = $this->record;
		
		if(is_numeric($this->toAddress)) {
			if(is_object($record)) {
				return $record->getAnswerValue($this->toAddress);
			}
		} else {
			return $this->toAddress;
		}
	}
	
	public function getReply() {
		
		$record = $this->record;
		
		if(is_numeric($this->replyAddress)) {
			if(is_object($record)) {
				return $record->getAnswerValue($this->replyAddress);
			}
		} else {
			return $this->replyAddress;
		}
	}
	
	public function verifyCondition() {
		
		if($this->conditionType == '') {
			return true;
		} else {
		
			$record = $this->record;
			$comparisonValue = $record->getAnswerValue($this->conditionFieldID);
			
			switch($this->conditionType) {
				case '=':
					if($this->conditionValue == $comparisonValue) {
						return true;
					}
					break;
				case '!=':
					if($this->conditionValue != $comparisonValue) {
						return true;
					}
					break;
				case '~':
					if(strpos($comparisonValue,$this->conditionValue) !== false) {
						return true;
					}
					break;
				case '!~':
					if(strpos($comparisonValue,$this->conditionValue) === false) {
						return true;
					}
					break;
			}
			
		}
	}
	
}