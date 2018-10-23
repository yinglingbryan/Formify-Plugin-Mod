<?php  
namespace Concrete\Package\Formify\Controller\Api;

use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Package;
use Loader;
use Log;
	
class Formify extends Controller {
	
	protected function canAccess() {
	    $formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
		
	function setConfig($property,$value) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$pkg = Package::getByHandle('formify');
		$pkg->getConfig()->save('formify.' . $property,$value);
	}
		
	function getConfig($property,$format='') {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$pkg = Package::getByHandle('formify');
		$value = $pkg->getConfig()->get('formify.' . $property);
		
		switch ($format) {
			case 'boolean':
				(bool) $value;
				break;
		}
		echo $value;
	}
	
}