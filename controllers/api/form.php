<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Loader;
use Log;
	
class Form extends Controller {
	
	protected function canAccess() {
    	$formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
	
	public function create() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$json = Loader::helper('json');
		$fJSON = $json->decode($json->encode($_POST));
		$f = \Concrete\Package\Formify\Src\FormifyForm::create($fJSON->name);
		if($fJSON->email != '') {
			$n = $f->addNotification();
			$n->set('type','add');
			$n->set('toAddress',$fJSON->email);
			$n->set('tID','formify_detail');
		}
		$this->outputJSON($f);
	}
	
	public function one($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$f->getRecordCount();
		$f->getIntegrations();
		$f->getPermissions();
		$this->outputJSON($f);
	}
	
	public function update($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$json = Loader::helper('json');
		$fJSON = $json->decode($json->encode($_POST));
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		foreach($fJSON as $property => $value) {
			$f->set($property,$value);
		}
	}
	
	public function integration($fID,$handle) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$f->toggleIntegration($handle);
	}
	
	public function permission($fID,$type,$gID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$f->togglePermission($type,$gID);
	}
	
	public function all() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$forms = \Concrete\Package\Formify\Src\FormifyForm::getAll();
		$json = Loader::helper('json');
		$this->outputJSON($forms);
	}
	
	public function delete($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		if($f = \Concrete\Package\Formify\Src\FormifyForm::get($fID)) {
			$f->delete();
		}
	}
	
	protected function outputJSON($data,$debug = false) {
		
		$json = Loader::helper('json');
		
		if(($debug) || ($_GET['debug'] == 1)) {
			echo '<pre>';
			echo json_encode($data, JSON_PRETTY_PRINT);
			echo '</pre>';
		} else {
			header('Content-Type: application/json');
			echo $json->encode($data);
		}
	}
	
}