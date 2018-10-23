<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyField;
use \Concrete\Package\Formify\Src\FormifyRecord;
use \Concrete\Package\Formify\Src\FormifyRecordSet;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Loader;
use Log;

class Export extends Controller {
	
	protected function canAccess() {
	    $formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
	
	public function run() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$debug = false;
		
		if(!$debug) {
			header('Expires: 0');
			header('Cache-control: private');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-disposition: attachment; filename=export.' . time() . '.csv');
		}
		
		
		$fID = $_POST['fID'];
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$fields = $f->getFields();
		
		$rs = \Concrete\Package\Formify\Src\FormifyRecordSet::get($fID);
		$rs->setPage(0);
		$rs->setPageSize(0);
		
		if($_POST['records'] == 'range') {
			$start = new \DateTime($_POST['start']);
			$end = new \DateTime($_POST['end']);
			$rs->setDateRange($start->getTimestamp(),$end->getTimestamp());
		}
		
		$records = $rs->getRecords();
		
		if($debug) {
			echo '<pre>';
		}
		
		if($_POST['includeRecordID'] == 'true') {
			echo '"Record ID",';
		}
		
		if($_POST['includeDateSubmitted'] == 'true') {
			//Print the date
			echo '"Date Submitted",';
		}
		
		if($_POST['includeIPAddress'] == 'true') {
			//Print the IP address
			echo '"IP Address",';
		}
		
		if($_POST['includeOwner'] == 'true') {
			//Print the username
			echo '"Owner",';
		}
		
		foreach($fields as $field) {
			if($_POST['fields'][$field->ffID] == true) {
				echo '"' . str_replace("\r\n","",str_replace('"','',$field->label)) . '",';
			}
		}
		
		echo "\n";
		
		foreach($records as $r) {
			set_time_limit(500);
			
			$firstColumn = true;
			
			if($_POST['includeRecordID'] == 'true') {
				echo '"' . $r->rID . '",';
			}
			
			if($_POST['includeDateSubmitted'] == 'true') {
				//Print the date
				echo '"' . date('Y-m-d g:i:s a',$r->updated) . '",';
			}
			
			if($_POST['includeIPAddress'] == 'true') {
				//Print the IP address
				echo '"' . $r->ipAddress . '",';
			}
			
			if($_POST['includeOwner'] == 'true') {
				//Print the username
				echo '"' . $r->username . '",';
			}
			
			foreach($fields as $field) {
				
				/*
				if(!$firstColumn) {
					echo ',';
				}
				*/
				
				if($_POST['fields'][$field->ffID] == true) {
					echo '"' . str_replace("\r\n",",",str_replace('"','',$r->getFriendlyAnswerValue($field->ffID))) . '",';
				}
			}
	
			echo "\n";
			
		}
		
		if($debug) {
			echo '</pre>';
		}
		
	}
	
}