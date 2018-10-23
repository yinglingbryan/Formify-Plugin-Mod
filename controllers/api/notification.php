<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyNotification;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Loader;
use Log;
	
class Notification extends Controller {
	
	protected function canAccess() {
	    $formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
	
	public function create($fID,$type='add') {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$json = Loader::helper('json');
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$n = $f->addNotification();
		$n->set('type',$type);
		
		if(($type == 'add') || ($type == 'update')) {
			$n->set('tID','formify_detail');
		}
		
		$json = Loader::helper('json');
		echo $json->encode($n);
	}
	
	public function one($nID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$n = \Concrete\Package\Formify\Src\FormifyNotification::get($nID);
		$json = Loader::helper('json');
		echo $json->encode($n);
	}
	
	public function update($nID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$json = Loader::helper('json');
		$nJSON = $json->decode($json->encode($_POST));
		$n = \Concrete\Package\Formify\Src\FormifyNotification::get($nID);
		foreach($nJSON as $property => $value) {
			$n->set($property,$value);
		}
	}
	
	public function all($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$notifications = $f->getNotifications();
		$json = Loader::helper('json');
		echo $json->encode($notifications);
	}
	
	public function delete($nID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
		
		if($n = \Concrete\Package\Formify\Src\FormifyNotification::get($nID)) {
			$n->delete();
		}
	}
	
}