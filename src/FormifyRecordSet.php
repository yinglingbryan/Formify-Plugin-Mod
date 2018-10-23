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
use Log;

class FormifyRecordSet {
	
	function get($fID) {
		if($f = \Concrete\Package\Formify\Src\FormifyForm::get($fID)) {
			$rs = new self;
			$rs->f = $f;
			$rs->fID = $fID;
			$rs->pageNum = 1;
			$rs->pageSize = 10;
			$rs->startDate = '';
			$rs->endDate = '';
			$rs->query = '';
			$rs->sortOrder = 'DESC';
			$rs->sortField = 0;
			$rs->requireOwnership = false;
			$rs->requireApproval = false;
			$rs->includeExpired = false;
			$rs->isPrePopulated = false;
			$rs->includeUpdated = false;
			$rs->records = array();
			return $rs;
		} else {
			return false;
		}
	}
	
	function includeExpired($value=true) {
		if($value) {
			$this->includeExpired = true;
		} else {
			$this->includeExpired = false;
		}
	}
	
	function includeUpdated($value=true) {
		if($value) {
			$this->includeUpdated = true;
		} else {
			$this->includeUpdated = false;
		}
	}
	
	function requireApproval($value=true) {
		if($value) {
			$this->requireApproval = true;
		} else {
			$this->requireApproval = false;
		}
	}
	
	function requireOwnership($value=true) {
		if($value) {
			$this->requireOwnership = true;
		} else {
			$this->requireOwnership = false;
		}
	}

	function setQuery($q) {
		$this->query = $q;
	}
	
	function getQuery() {
		if($this->query == '') {
			return false;
		} else {
			$words = explode(' ',$this->query);
			return $words;
		}
	}
	
	function setSortOrder($order = '') {
		if($order == 'DESC') {
			$this->sortOrder = 'DESC';
		} elseif ($order == 'RAND') {
			$this->sortOrder = 'RAND';
		} else {
			$this->sortOrder = 'ASC';
		}
	}
	
	function getSortOrder() {
		return $this->sortOrder;
	}
	
	function setSortField($field) {
		if(is_numeric($field)) {
			$this->sortField = intval($field);
		} else {
			$field = \Concrete\Package\Formify\Src\FormifyField::get($field,$this->fID);
			$this->sortField = $field->ffID;
		}
	}
	
	function getSortFieldID() {
		return intval($this->sortField);
	}
	
	function setPage($p) {
		$this->pageNum = intval($p);
	}
	
	function getPage() {
		return $this->pageNum;
	}
	
	function setPageSize($s) {
		$this->pageSize = intval($s);
	}
	
	function getPageSize() {
		return $this->pageSize;
	}

	function setDateRange($startDate,$endDate,$eod=true) {
		//Date format should be Unix timestamp
		$this->startDate = $startDate;
		if($eod) {
			$this->endDate = $endDate + 86400; //Add 86,400 to make it the END of that day
		} else {
			$this->endDate = $endDate;
		}
	}
	
	function setRecordData() {
		//Task:		Determine which records to get without getting the full record data
		//Reason: 	Outside of the filtering, we could just setup a long SQL query, but the
		//			filtering requires some extra SQL queries to check the actual record data.
		
		$db = Loader::db();
		$SQLvars = array();
		$SQLvars[] = $this->fID;	
		
		// 1. Setup sort order SQL
		$sortBy = 'sortPriority';
		
		if($this->getSortFieldID() != 0) {
			$sortSQL = 'AND answers LIKE \'%"ffID":"' . $this->getSortFieldID() . '"%\'';
			$sortBy = 'SUBSTRING_INDEX( SUBSTRING_INDEX( SUBSTRING_INDEX( answers, \'"ffID":"' . $this->getSortFieldID() . '"\', -1 ), \'"]\', 1 ), \'"value":["\',-1)';
		}
		
		if($this->getSortOrder() == 'RAND') {
			$sortOrder = 'RAND()';
			$sortBy = '';
		} else {
			$sortOrder = $this->getSortOrder();
		}
		
		
		
		// 3. Get approved records only?			
		if($this->requireApproval) {
			$approvalSQL = 'AND approval = 1';
		} else {
			$approvalSQL = '';
		}
		
		// 4. Only show records owned by the current user?
		if($this->requireOwnership) {
			$owner = new User();
			$ownershipSQL = "AND uID = " . intval($owner->getUserID());
		} else {
			$ownershipSQL = '';
		}
		
		// 5. Search according to the date range specified
		if($this->startDate != '') {
			$startDateSQL = "AND created >= " . intval($this->startDate);
		} else {
			$startDateSQL = '';
		}
		
		if($this->endDate != '') {
			$endDateSQL = "AND created <= " . intval($this->endDate);
		} else {
			$endDateSQL = '';
		}
		
		// 6. Is there are search query?
		$q = $this->getQuery();
		if($q) {
			if(count($q) == 1) {
				$searchSQL = "AND searchIndex LIKE ?";
				$SQLvars[] = '%' . $q[0] . '%';
			} else {
				$searchSQL = "AND (";
				$i = 0;
				foreach($q as $word) {
					if($i > 0) {
						$searchSQL .= ' AND ';
					}
					$searchSQL .= "searchIndex LIKE ?";
					$SQLvars[] = '%' . $word . '%';
					$i++;
				}
				$searchSQL .= ')';
			}
		}
		
		// 7. Get the record ID's
		$pageSize = $this->getPageSize();
		$startAt = ($this->getPage() - 1) * $pageSize;
		if($startAt < 0) {
			$startAt = 0;
		}
		
		if($this->getPageSize() == 0) {
			$limitSQL = '';
		} else {
			$limitSQL = "LIMIT $startAt, $pageSize";
		}
		
		$query = "SELECT * FROM " . TABLE_FORMIFY_RECORDS . " WHERE fID = ? AND isDeleted != 1 $expiredSQL $approvalSQL $ownershipSQL $startDateSQL $endDateSQL $searchSQL $sortSQL ORDER BY $sortBy $sortOrder $limitSQL";
		Log::addEntry($query);
		
		$this->records = $db->getAll($query,$SQLvars);
		$this->isPrePopulated = true;
	}
	
	function getRecordData() {
		if($this->isPrePopulated) {
			return $this->records;
		} else {
			$this->setRecordData();
			return $this->records;
		}
	}
	

	function getRecords() {
		if(!$this->isPrePopulated) {
			//If the record list has not been pre populated, do so now
			$this->getRecordData();
		}
		
		//Get the record data according to the records returned in prePopulate
		$records = array();
		foreach($this->records as $rData) {
			$records[] = \Concrete\Package\Formify\Src\FormifyRecord::get($rData);
		}
		
		return $records;
	}
	
	function clearRecords() {
		$this->records = false;
		$this->isPrePopulated = false;
	}
	
	function getRecordCount() {
		if(!$this->isPrePopulated) {
			//If the record list has not been pre populated, do so now
			$this->getRecordIDs();
		}
		return count($this->getRecordIDs());
	}
	
	function getPageCount() {
		if(!$this->isPrePopulated) {
			//If the record list has not been pre populated, do so now
			$this->getRecordIDs();
		}
	}
	
	function getDateFormat() {
		
		$field = \Concrete\Package\Formify\Src\FormifyField::getByID($this->getSortFieldID());
	
		if($field->dateFormat != '') {
			$inputFormat = $field->dateFormat;	
		} else {
			$inputFormat = 'mm/dd/yy';
		}
	
	    $pattern = array(
	        // Day
	        'dd', // Day of month, two digit
	        'd', // Day of month, no leading zero
	        'oo', // Day of year, three digit
	        'o', // Day of year, no leading zero
	        'DD', // Day name, long
	        'D', // Day name, short
	        // Month
	        'mm', // Month, two digit
	        'm', // Month, no leading zero
	        'MM', // Month name, long
	        'M', // Month name, short
	        // Year
	        'yy', // Year, four digit
	        'y', // Year, two digit
	    );
	    
	    $tmpReplace = array(
	    	// Day
	        '%1', // Day of month, two digit
	        '%2', // Day of month, no leading zero
	        '%3', // Day of year, three digit
	        '%4', // Day of year, no leading zero
	        '%5', // Day name, long
	        '%6', // Day name, short
	        //Month
	        '%7', // Month, two digit
	        '%8', // Month, no leading zero
	        '%9', // Month name, long
	        '%0', // Month name, short
	        // Year
	        '%x', // Year, four digit
	        '%z', // Year, two digit
	    );
	    
	    $replace = array(
	    	// Day
	        '%d', // Day of month, two digit
	        '%e', // Day of month, no leading zero
	        '%j', // Day of year, three digit
	        '%j', // Day of year, no leading zero
	        '%W', // Day name, long
	        '%a', // Day name, short
	        // Month
	        '%m', // Month, two digit
	        '%c', // Month, no leading zero
	        '%M', // Month name, long
	        '%b', // Month name, short
	        // Year
	        '%Y', // Year, four digit
	        '%y', // Year, two digit
	    );
	    
	    foreach($pattern as &$p) {
			$p = '/' . $p . '/';
	    }
		
		$tmpFormat = preg_replace($pattern,$tmpReplace,$inputFormat);
		
		foreach($tmpReplace as &$p) {
			$p = '/' . $p . '/';
	    }
		
		$outputFormat = preg_replace($tmpReplace,$replace,$tmpFormat);
		
		return $outputFormat;
	}	
	
}