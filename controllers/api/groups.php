<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyGroup;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Loader;
use Log;

class Groups extends Controller {
	
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
        $gJSON = $json->decode($json->encode($_POST));
        
        Log::addEntry(print_r($gJSON,true));
        
		$g = \Concrete\Package\Formify\Src\FormifyGroup::create($gJSON->name);
		$json = Loader::helper('json');
		echo $json->encode($g);
	}
	
	public function all() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$groups = \Concrete\Package\Formify\Src\FormifyGroup::all();
		$json = Loader::helper('json');
		echo $json->encode($groups);
	}
	
	public function delete($gID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$g = \Concrete\Package\Formify\Src\FormifyGroup::get($gID);
		$g->delete();
	}
	
	public function update($gID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$json = Loader::helper('json');
		$gJSON = $json->decode($json->encode($_POST));
		$g = \Concrete\Package\Formify\Src\FormifyGroup::get($gID);
		foreach($gJSON as $property => $value) {
			$g->set($property,$value);
		}
	}
	
}