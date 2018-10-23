<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyField;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Loader;
use Log;

class Options extends Controller {
	
	protected function canAccess() {
	    $formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
	
	public function create() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$og = \Concrete\Package\Formify\Src\FormifyOptionGroup::create($_GET['name']);
		$json = Loader::helper('json');
		echo $json->encode($og);
	}
	
	public function all() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$optionGroups = \Concrete\Package\Formify\Src\FormifyOptionGroup::all();
		$json = Loader::helper('json');
		echo $json->encode($optionGroups);
	}
	
	public function delete($ogID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$og = \Concrete\Package\Formify\Src\FormifyOptionGroup::get($ogID);
		$og->delete();
	}
	
	public function update($ogID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$json = Loader::helper('json');
		$ogJSON = $json->decode($json->encode($_POST));
		$og = \Concrete\Package\Formify\Src\FormifyOptionGroup::get($ogID);
		foreach($ogJSON as $property => $value) {
			$og->set($property,$value);
		}
		
		$og->setOptions($ogJSON->optionsValues);
	}
	
}