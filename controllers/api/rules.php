<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyField;
use \Concrete\Package\Formify\Src\FormifyRule;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Loader;
use Log;

class Rules extends Controller {
	
	protected function canAccess() {
	    $formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
	
	public function create($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$r = \Concrete\Package\Formify\Src\FormifyRule::create($fID);
		$json = Loader::helper('json');
		echo $json->encode($r);
	}
	
	public function all($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$rules = \Concrete\Package\Formify\Src\FormifyRule::all($fID);
		$json = Loader::helper('json');
		echo $json->encode($rules);
	}
	
	public function delete($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$r = \Concrete\Package\Formify\Src\FormifyRule::get($rID);
		$r->delete();
	}
	
	public function update($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$json = Loader::helper('json');
		$rJSON = $json->decode($json->encode($_POST));
		$r = \Concrete\Package\Formify\Src\FormifyRule::get($rID);
		foreach($rJSON as $property => $value) {
			$r->set($property,$value);
		}
	}
	
}