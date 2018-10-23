<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyField;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use Loader;
use Log;

class Fields extends Controller {
	
	protected function canAccess() {
	    $formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
	
	public function create($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$ff = $f->addField();
		$json = Loader::helper('json');
		echo $json->encode($ff);
	}
	
	public function all($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		$fields = $f->getFields();
		$this->outputJSON($fields);
	}
	
	public function one($ffID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$ff = \Concrete\Package\Formify\Src\FormifyField::get($ffID);
		$this->outputJSON($ff);
	}
	
	public function delete($ffID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$ff = \Concrete\Package\Formify\Src\FormifyField::get($ffID);
		$ff->delete();
	}
	
	public function update($ffID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$json = Loader::helper('json');
		$ffJSON = $json->decode($json->encode($_POST));
		$ff = \Concrete\Package\Formify\Src\FormifyField::get($ffID);
		$f = $ff->getForm();
		foreach($ffJSON as $property => $value) {
			$ff->set($property,$value);
		}
		
		$ff->setOptions($ffJSON->optionsValues);
		$ff->setRules($ffJSON->rules);
		
		//Set integration config
		if(count($f->getActiveIntegrations()) > 0) {
			foreach($f->getActiveIntegrations() as $i) {
				$iHandle = $i->handle;
				if(count($ffJSON->integrationConfig->$iHandle) > 0) {
					foreach($ffJSON->integrationConfig->$iHandle as $key => $value) {
						$ff->setIntegrationConfig($iHandle,$key,$value);
					}
				}
			}
		}
		
	}
	
	public function resort() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$json = Loader::helper('json');
		$fieldsJSON = $json->decode($json->encode($_POST));
		
		$i = 1;
		foreach($fieldsJSON as $ffID) {
			$ff = \Concrete\Package\Formify\Src\FormifyField::get($ffID);
			$fieldlist .= $ff->label . '<br />';
			$ff->set('sortPriority',$i);
			$i++;
		}
	}
	
	public function types() {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$fieldTypes = \Concrete\Package\Formify\Src\FormifyFieldType::all();
		$json = Loader::helper('json');
		echo $json->encode($fieldTypes);
	}
	
	protected function outputJSON($data,$debug = false) {
		
		$json = Loader::helper('json');
		
		if(($debug) || ($_GET['debug'] == 1)) {
			echo '<pre>';
			echo $json->encode($this->encodeObject($data), JSON_PRETTY_PRINT);
			//print_r($this->encodeObject($data));
			echo '</pre>';
		} else {
			header('Content-Type: application/json');
			echo $json->encode($this->encodeObject($data));
		}
	}
	
	public function encodeObject($obj) {
		if(is_array($obj)) {
			if(count($obj) == 0) {
				return $obj;
			}
		}
	    foreach ($obj as $key => $value) {
	        if(is_object($value)) {
		        $arr[$key] = $this->encodeObject($value);
	        } elseif(is_array($value)) {
		        if(count($value) == 0) {
			        $arr[$key] = array();
		        } else {
			        $arr[$key] = $this->encodeObject($value);
		        }
	        } else {
		        $arr[$key] = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
		        
	        }
	    }
	    return $arr;
    }
	
}