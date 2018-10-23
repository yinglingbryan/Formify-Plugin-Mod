<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyField;
use \Concrete\Package\Formify\Src\FormifyRule;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Loader;
use Log;

class Templates extends Controller {
	
	protected function canAccess() {
	    $formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
	
	public function create($name) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$t = \Concrete\Package\Formify\Src\FormifyTemplate::create($name);
		$json = Loader::helper('json');
		echo $json->encode($t);
	}
	
	public function all() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$templates = \Concrete\Package\Formify\Src\FormifyTemplate::all();
		$json = Loader::helper('json');
		echo $json->encode($templates);
	}
	
	public function delete($tID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$t = \Concrete\Package\Formify\Src\FormifyTemplate::get($tID);
		$t->delete();
	}
	
	public function update($tID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$json = Loader::helper('json');
		$tJSON = $json->decode($json->encode($_POST));
		$t = \Concrete\Package\Formify\Src\FormifyTemplate::get($tID);
		foreach($tJSON as $property => $value) {
			$t->set($property,$value);
		}
	}
	
}