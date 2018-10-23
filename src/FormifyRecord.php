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
use User;
use File;
use Log;

class FormifyRecord {
  
  private $assignableProperties = array(
    'updated',
    'ipAddress',
    'uID',
    'sortPriority',
    'token',
    'name',
    'amountCharged',
    'amountPaid',
    'expiration',
    'searchIndex',
    'matchingFilter',
    'amountCharged',
    'amountPaid',
    'source',
    'referrer'
  );
  
  public function get($rID) {
    
    if(is_array($rID)) {
      $rData = $rID;
      $rID = $rData['rID'];
    } else {
      $db = Loader::db();
      $rData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_RECORDS . " WHERE rID = ? AND isDeleted != 1",array($rID));
    }
    
    if(($rData['rID'] == $rID) && ($rData['rID'] != 0)) {
      
      $r = new self;
      
      foreach($rData as $col => $val) {
        if($val != '') {
          $r->$col = $val;
        }
      }
        
      $r->created = $r->created * 1000;
        
      if($r->uID > 0) {
        $u = User::getByUserID($r->uID);
        $r->username = $u->getUsername();
      }
      
      switch($r->approval) {
        case 1;
          $r->status = 'approved';
          break;
        case -1:
          $r->status = 'rejected';
          break;
        default:
          $r->status = 'pending';
      }
      
      $json = Loader::helper('json');
      
      
      $r->answers = $json->decode($r->answers);
      
      if(count($r->answers) > 0) {
        foreach($r->answers as $key => $a) {
          $r->answers[$key]->friendlyValue = $r->getFriendlyAnswerValue($a->ffID);
        }
      }
      
      $r->name = $r->getName();
      
      unset($r->cache);
      
      return $r;
      
    } else {
      return false;
    }
  }
  
  public function getWithToken($rID,$token) {
    $db = Loader::db();
    $rData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_RECORDS . " WHERE rID = ? AND token = ? AND isDeleted != 1",array($rID,$token));
    if(($rData['rID'] == $rID) && ($rData['rID'] != 0)) {
      return self::get($rData['rID']);
    } else {
      return false;
    }
  }
  
  public function create($f) {
    $db = Loader::db();
    
    if(!is_object($f)) {
      $f = \Concrete\Package\Formify\Src\FormifyForm::get($f);
    }
    
    $time = time();
    
    $u = new User();
    $uID = $u->getUserID();
    
    $token = self::generateToken();
    
    $db->execute("INSERT INTO " . TABLE_FORMIFY_RECORDS . " (rID,fID,created,updated,ipAddress,uID,token) VALUES (0,?,?,?,?,?,?)",array($f->fID,$time,$time,$_SERVER['REMOTE_ADDR'],$uID,$token));
    
    $r = self::get($db->Insert_ID());
    $r->f = $f;
    $r->set('sortPriority',$r->rID);
    
    return $r;
  }
  
  public function delete() {
    if($this->userCanDelete()) {
      $db = Loader::db();
      $db->execute("UPDATE " . TABLE_FORMIFY_RECORDS . " SET isDeleted = 1 WHERE rID = ?",array($this->rID));
    }
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
    if(($this->propertyIsAssignable($property)) || ($force == true)) {
      $db->replace(TABLE_FORMIFY_RECORDS,array('rID'=>$this->rID,$property=>$value),'rID');
    }
    $this->$property = $value;
  }
  
  public function getForm() {
    if(is_object($this->f)) {
      return $this->f;
    } else {
      $this->f = \Concrete\Package\Formify\Src\FormifyForm::getByID($this->fID);
      return $this->getForm();
    }
  }
  
  public function getName() {
    $name = '';
    
    if(count($this->getAnswers()) > 0) {
      foreach($this->getAnswers() as $a) {
        if($a->isPrimary == '1') {
          $name .= strip_tags($this->getAnswerValue($a->ffID) . ' ');
        }
      }
    }
    
    $name = trim($name);
    
    if($name == '') {
      $name = $this->getFirstAnswerValue();
    }
    
    return $name;
  }
  
  public function getFirstAnswerValue() {
    $answers = $this->getAnswers();
    return $this->getAnswerValue($answers[0]->ffID);
  }
  
  public function generateToken() {
    $token = '';
    srand((double)microtime()*1000000);
    $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
    $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
    $data .= "0FGH45OP89";
    for($i = 0; $i < 10; $i++) {
      $token .= substr($data, (rand()%(strlen($data))), 1);
    }
    
    return $token;
  }
  
  public function clear() {
    $db = Loader::db();
    $db->execute("UPDATE " . TABLE_FORMIFY_RECORDS . " SET answers = '' WHERE rID = ?",array($this->rID));
    unset($this->answers);
  }
  
  public function addAnswer($field,$answerValue,$save=true) {
    
    $answer = $this->prepareAnswer($field,$answerValue);
    
    //Add new answer to answers array
    $this->answers[] = $answer;
    
    if($save) { 
      $this->saveAnswers();
    }
  }
  
  public function prepareAnswer($field,$answerValue) {
    
    if(!is_object($field)) {
      $field = $this->getForm()->getField($field);
    }
    
    $answer = new \stdClass;
    $answer->ffID = $field->ffID;
    $answer->label = $field->label;
    $answer->type = $field->type;
    $answer->handle = $field->handle;
    $answer->isPrimary = $field->isPrimary;
    $answer->isIndexable = $field->isIndexable;
    $answer->includeInEmail = $field->includeInEmail;
    $answer->value = $this->prepareAnswerValue($field,$answerValue);
    
    return $answer;
    
  }
  
  public function prepareAnswerValue($field,$answerValue) {
    
    switch($field->type) {
      case 'date':
        if($answerValue != '') {
          if(is_array($answerValue)) { // Date value is in array parts
              $dateString = $answerValue[0] . '-' . $answerValue[1] . '-' . $answerValue[2];
              if($dateString != '--') {
                $dt = \DateTime::createFromFormat('n-j-Y',$dateString);
                        }
          } else { // Date value is string
            if($field->dateFormat != '') {
              $dt = \DateTime::createFromFormat($field->dateFormat,$answerValue);
            } else {
              $dt = \DateTime::createFromFormat('F j, Y',$answerValue);
            }
            
          }
                
            if($dt instanceof \DateTime) {
              $answerValue = $dt->getTimestamp();
            }
        }
        
        break;
    }
    
    $value = array();
    
    if(is_array($answerValue)) {
      for($i=0;$i<count($answerValue);$i++) {
        $value[] = mb_convert_encoding($answerValue[$i],'UTF-8','UTF-8');
      }
    } else {
      $value[] = mb_convert_encoding($answerValue,'UTF-8','UTF-8');
    }
    
    return $value;
    
  }
  
  public function saveAnswers() {
    $db = Loader::db();
    $json = Loader::helper('json');
    $answers = $json->encode($this->answers);
    $db->execute("UPDATE " . TABLE_FORMIFY_RECORDS . " SET answers = ? WHERE rID = ?",array($answers,$this->rID));
  }
  
  public function getAnswers() {
    return $this->answers;
  }
  
  public function getAnswer($id) {
    if(is_numeric($id)) { // Get by ffID
      if(count($this->getAnswers()) > 0) {
        foreach($this->getAnswers() as $a) {
          if($a->ffID == $id) {
            return $a;
          }
        }
      }
    } else { // Get by handle
      if(count($this->getAnswers()) > 0) {
        foreach($this->getAnswers() as $a) {
          if($a->handle == $id) {
            return $a;
          }
        }
      }
    }
  }
  
  public function getAnswerValue($id,$index=0) {
    $a = $this->getAnswer($id);
    switch($a->type) {
      case 'time':
        return $a->value[0] . ':' . $a->value[1] . ' ' . $a->value[2];
        break;
      default:
        return $this->getAnswer($id)->value[$index];
    }
  }
  
  public function getFriendlyAnswerValue($id,$index=0) {
    $a = $this->getAnswer($id);
    $value = $this->getAnswer($id)->value[$index];
    
    if(($a->type == 'attachment') || ($a->type == 'file')) {
      $file=File::getByID($value);
      if(($file) && (is_numeric($value))) {
        $fv=$file->getApprovedVersion();
        $value = '<a href="' . $fv->getURL() . '">' . $fv->getFileName() . '</a>';
        //$value = $fv->getFileName();
      } else {
        $value = '';
      } 
    } elseif($a->type == 'date') {
        if($value != '') {
            $dt = new \DateTime();
            if($dt->setTimestamp($value)) {
                $value = $dt->format('F j, Y');
                }
            }
    } elseif($a->type == 'time') {
      $value = $a->value[0] . ':' . $a->value[1] . ' ' . $a->value[2];
    } elseif(count($a->value) > 1) {
      $value = '';
      foreach($a->value as $v) {
        $value .= $v . '<br />';
      }
    }
    
    return $value;
  }
  
  public function index() {
    $f = $this->getForm();
    $index = '';
    foreach($f->getFields() as $ff) {
      if($ff->isIndexable) {
        $index .= strip_tags($this->getAnswerValue($ff->ffID) . ' ');
      }
    }
    $this->set('searchIndex',trim($index));
    $this->rebuildAnswers();
  }
  
  public function rebuildAnswers() {
    
    $newAnswers = array();
    
    //Rebuild answers array
    if(count($this->getAnswers()) > 0) {
      foreach($this->getAnswers() as $a) {
        $newAnswers[] = $this->prepareAnswer($a->ffID,$a->value);
      }
    }
    
    $this->answers = $newAnswers;
    $this->saveAnswers();
  }
  
  public function resort($adjacentRecordID) {
    $db = Loader::db();
    
    $adjacentRecord = self::get($adjacentRecordID);
    
    
    $oldPriority = $this->sortPriority;
    $newPriority = $adjacentRecord->sortPriority;
    
    if($newPriority > $oldPriority) {
      //Record is moving up so move other records down
      $recordsToMove = $db->getAll("SELECT rID, sortPriority FROM " . TABLE_FORMIFY_RECORDS . " WHERE fID = ? AND sortPriority <= ? AND sortPriority > ? AND isDeleted != 1",array($this->fID,$newPriority,$oldPriority));
      foreach($recordsToMove as $r) {
        $db->execute("UPDATE " . TABLE_FORMIFY_RECORDS . " SET sortPriority = ? WHERE rID = ?",array(($r['sortPriority'] - 1),$r['rID']));
      }
    } else {
      //Record is moving down so move other records up
      $recordsToMove = $db->getAll("SELECT rID, sortPriority FROM " . TABLE_FORMIFY_RECORDS . " WHERE fID = ? AND sortPriority < ? AND sortPriority >= ? AND isDeleted != 1",array($this->fID,$oldPriority,$newPriority));
      foreach($recordsToMove as $r) {
        $db->execute("UPDATE " . TABLE_FORMIFY_RECORDS . " SET sortPriority = ? WHERE rID = ?",array(($r['sortPriority'] + 1),$r['rID']));
      }
    }
    
    $this->set('sortPriority',$newPriority);
    
  }
  
  public function userCanEdit() {
    $u = new User();
    
    if($u->isSuperUser()) { //Super user can always edit
      return true;
    }
    
    //Check whether users can edit their own records
    $f = $this->getForm();
    if($f->ownerCanEdit()) {
      if($u->isRegistered() === false) { //Guest user cannot own records, so it can't edit a specific record
        return false;
      } else {
        if(intval($u->getUserID()) == intval($this->uID)) { //If the user owns the record, they can edit it
          return true;
        }
      }
    } else { //If users cannot edit their own records, they must be part of a group that can edit records
      foreach($u->uGroups as $gID => $gName) { //Loop through the groups
        if($f->groupCanEdit($gID)) { //If user is part of a group that can edit, they can edit
          return true;
        }
      }
    }
    
    return false; //Deny access by default
  }
  
  public function userCanDelete() {
    $u = new User();
    
    if($u->isSuperUser()) { //Super user can always delete
      return true;
    }
    
    //Check whether users can delete their own records
    $f = $this->getForm();
    if($f->ownerCanDelete) {
      if($u->isRegistered() === false) { //Guest user cannot own records, so it can't delete a specific record
        return false;
      } else {
        if(intval($u->getUserID()) == intval($this->uID)) { //If the user owns the record, they can delete it
          return true;
        }
      }
    } else { //If users cannot delete their own records, they must be part of a group that can delete records
      foreach($u->uGroups as $gID => $gName) { //Loop through the groups
        if($f->groupCanDelete($gID)) { //If user is part of a group that can delete, they can edit
          return true;
        }
      }
    }
    
    return false; //Deny access by default  
  }
  
  public function approve($force=false) {
    $db = Loader::db();
    
    $f = $this->getForm();
    if(($f->userCanApprove()) || ($force)) {
      $db->execute("UPDATE " . TABLE_FORMIFY_RECORDS . " SET approval = 1 WHERE rID = ?",array($this->rID));
    }
  }
  
  public function reject($force=false) {
    $db = Loader::db();
    
    $f = $this->getForm();
    if(($f->userCanApprove()) || ($force)) {
      $db->execute("UPDATE " . TABLE_FORMIFY_RECORDS . " SET approval = -1 WHERE rID = ?",array($this->rID));
    }
  }
  
  public function pend($force=false) {
    $db = Loader::db();
    
    $f = $this->getForm();
    if(($f->userCanApprove()) || ($force)) {
      $db->execute("UPDATE " . TABLE_FORMIFY_RECORDS . " SET approval = 0 WHERE rID = ?",array($this->rID));
    }
  }
  
  public function processIntegrations() {
    $f = $this->getForm();
    foreach($f->getActiveIntegrations() as $i) {
      $i->process($this);
    }
  }
  
  /* Version 1.x to Version 2.0+ Migration Functions */
  
  public function migrate() {
    
    if(count($this->getAnswers()) == 0) { // Only run migration if answers column is empty
      
      $answers = $this->getAnswersFromDatabase();
      
      if(count($answers) > 0) {
        foreach($answers as $a) {
          // Delay saving answer until the end of the process to save server resources
          $this->addAnswer($a->ffID,$a->value,false);
        }
        $this->saveAnswers();
      }
      
    }
  }
  
  public function getAnswersFromDatabase() {
    $db = Loader::db();
    $answerData = $db->getAll("SELECT * FROM " . TABLE_FORMIFY_ANSWERS . " WHERE rID = ?",array($this->rID));
    $answers = array();
    
    foreach($answerData as $a) {
      $answer = new \stdClass();
      
      $answer->ffID = $a['ffID'];
      
      $json = Loader::helper('json');
      if($json->decode($a['value'])) {
        $answer->value = $json->decode($a['value']);
      } else {
        $answer->value = array($a['value']);  
      }
      
      if($a['isTimestamp'] == '1') {
        $ff = $this->getForm()->getField($a['ffID']);
        if($ff->dateFormat == '') {
          $answer->value = date('F j, Y',$answer->value[0]);
        } else {
          $answer->value = date($ff->dateFormat,$answer->value[0]);
        }
      }
      
      $answers[] = $answer;
    }
    
    return $answers;
  }
  
  /* End version 1.x to Version 2.0+ Migration Functions */
  
}