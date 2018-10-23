<?php  
namespace Concrete\Package\Formify\Controller\SinglePage\Dashboard\Formify;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\Formify\Src\ParseCSV;
use \Concrete\Package\Formify\Src\FormifyForm;
use Session;
use Loader;
use File;
use Log;

defined('C5_EXECUTE') or die("Access Denied.");

class Import extends DashboardPageController {

	public function view() {	
		$this->loadHeaderItems();
		$forms = \Concrete\Package\Formify\Src\FormifyForm::getAll();
		$this->set('forms',$forms);
	}
	
	public function parse() {
		$this->loadHeaderItems();
		$this->set('action','parse');
		
		$csvFile = File::getByID($_POST['fileID']);
		
		$rows = str_getcsv($csvFile->getFileResource()->read(),"\n");
		
		if(count($rows) == 1) {
			$rows = str_getcsv($csvFile->getFileResource()->read(),"\r\n");
		}
		
		foreach($rows as &$row) $row = str_getcsv($row, ",");
		
		if(intval($_POST['fID']) != 0) {
			$f = \Concrete\Package\Formify\Src\FormifyForm::get(intval($_POST['fID']));
			$this->set('f',$f);
			$this->set('fID',$f->fID);
		} else {
			$this->set('fID',0);
			$this->set('formName',$_POST['formName']);
		}
		
		$this->set('fileID',$_POST['fileID']);
		$this->set('rows',$rows);
		
	}
	
	public function run() {
		
		$time_start = microtime(true);
		
		$this->loadHeaderItems();
		$this->set('action','run');
		
		if($_POST['fID'] == 0) {
			//Create Form
			$f = \Concrete\Package\Formify\Src\FormifyForm::create($_POST['formName']);
		} else {
			$f = \Concrete\Package\Formify\Src\FormifyForm::get(intval($_POST['fID']));
		}
		
		$fields = array();
		
		if(count($_POST['import']) > 0) {
			foreach($_POST['import'] as $key => $column) {
				if($column == 'true') {
					if($_POST['ffID'][$key] == 0) {
						//Create field
						$ff = $f->addField();
						$ff->set('label',$_POST['label'][$key]);
						$fields[$key] = $ff;
					} else {
						$ff = \Concrete\Package\Formify\Src\FormifyField::get($_POST['ffID'][$key]);
						$fields[$key] = $ff;
					}
				}
			}
		}
		
		$csvFile = File::getByID($_POST['fileID']);
		
		$rows = str_getcsv($csvFile->getFileResource()->read(),"\n");
		
		if(count($rows) == 1) {
			$rows = str_getcsv($csvFile->getFileResource()->read(),"\r\n");
		}
		
		foreach($rows as &$row) $row = str_getcsv($row, ",");
		
		$importedRows = 0;
		$rowNum = 1;
		
		foreach($rows as $data) {
			
			set_time_limit(30);
			
			$proceed = true;
			
			if(($rowNum == 1) && ($_POST['ignoreFirstRow'] == 'true')) {
					$proceed = false;
			}
			
			if($proceed) {
			
				$r = $f->addRecord();
				
				$i = 1;
				foreach($data as $cell) {
					if($_POST['import'][$i] == 'true') {
						$r->addAnswer($fields[$i],$cell,false);
					}
					$i++;
				}
				
				$r->saveAnswers();
				
				$importedRows++;
				
			}
			
			$rowNum++;
			
		}
		
		$time_end = microtime(true);
		$execution_time = ($time_end - $time_start);
		Log::addEntry('Import time: ' . $execution_time);
		
		
		$this->set('fID',$f->fID);
		$this->set('count',$importedRows);
		
	}
	
	public function loadHeaderItems() {
		$html = Loader::helper('html');
		$this->addFooterItem($html->javascript('import.js','formify'));
		
		
	}
}