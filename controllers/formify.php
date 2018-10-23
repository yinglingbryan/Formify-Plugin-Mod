<?php  
namespace Concrete\Package\Formify\Controller;
use \Concrete\Core\Controller\Controller;

use \Concrete\Package\Formify\Form;
use Loader;
use Page;
use Permissions;
use Config;

class Formify extends Controller {

	public function changeMode() {
	
		$ch = Page::getByPath('/dashboard/formify');
		$chp = new Permissions($ch);
		if (!$chp->canRead()) {
			die(_("Access Denied."));
		}
		
		$mode = intval(Config::get('formify.discover_mode'));
		
		if($mode == 1) {
			Config::save('formify.discover_mode',0);
		} else {
			Config::save('formify.discover_mode',1);
		}
	
		echo Config::get('formify.discover_mode');
	}

	public function process() {
	
		$p = Formify::initialize();
		$p->getForm();
		
		
		$js = Loader::helper('json');
		echo $js->encode($p);
	}
	
	public function initialize() {
		$p = new Formify;
		
		//Set Processing Method
		$p->handle = $_GET['handle']; // Form Handle
		$p->ajax = $_GET['ajax']; // Post method
	
		//Set Required Variables
		$p->fID = intval($_POST['fID']); // Form ID
		$p->bID = $_POST['bID']; // Block ID
		$p->asID = $_POST['rID']; // Answer Set ID
		$p->editCode = $_POST['editCode']; // Answer Set Edit Code
		
		if(intval($p->fID) > 0) {
			$p->identifier = $p->fID;
		} else {
			$p->identifier = $p->handle;
		}
		
		return $p;
	}
	
	public function getForm() {
		if($this->identifier) {
			$f = Form::get($identifier);
		} elseif ($identifier != '') {
			$handle = $identifier;
			$f = sixeightForm::getByHandle($handle);
			if(!$f) {
				$fData = array();
				$fData['handle'] = $handle;
				$fData['name'] = $handle;
				$fData['sendMail'] = 0;
				$fData['afterSubmit'] = 'thankyou';
				$fData['thankyouMsg'] = t('Thank you for contacting us.');
				$f = sixeightForm::create($fData);
			}
		} else {
			return false;
		}
	}

}