<?php      
namespace Concrete\Package\Formify\Block\FormifyForm;

use \Concrete\Core\Block\BlockController;
use \Concrete\Package\Formify\Src\FormifyForm;
use Loader;
use Log;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends BlockController {

	protected $btTable = 'btFormifyForm';
	protected $btInterfaceWidth = "300";
	protected $btInterfaceHeight = "170";
    protected $btDefaultSet = 'formify';
	
	public function getBlockTypeName() {
		return t("Formify Form");
		}

	public function getBlockTypeDescription() {
		return t("Display a form created from the backend");
		}
	
	public function add() {
		$this->edit();
		}
	
	public function edit() {
		$forms = \Concrete\Package\Formify\Src\FormifyForm::getAll();
		if(count($forms) > 0) {
			$isReady = true;
		} else {
			$isReady = false;
		}
		$this->set('forms',$forms);
		$this->set('isReady',$isReady);
	}
	
	public function view() {
		$this->requireAsset('redactor');
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($this->fID);
		$this->set('f',$f);
		
		$this->set('context',$this->context);
		$r = \Concrete\Package\Formify\Src\FormifyRecord::getWithToken($this->rID,$this->token);
		
		if($f->captcha) {
			$this->requireAsset('css', 'core/frontend/captcha');
			$this->set('captcha',Loader::helper('validation/captcha'));
		} else {
			$this->set('captcha',false);
		}
		
		if(is_object($r)) {
			$fields = $f->getFields();
			foreach($fields as $key => $ff) {
				if($r->getAnswerValue($ff->ffID) != '') {
					$fields[$key]->defaultValue = $r->getAnswerValue($ff->ffID);
				}
			}
			
			$this->set('rID',$this->rID);
			$this->set('token',$this->token);
		}
		
	}
	
	public function save($data) {
		parent::save($data);
	}
	
	public function on_page_view() {
		$this->checkSSL();
		$this->loadHeaderItems();
		$this->loadFooterItems();
		
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('font-awesome.css'));
	}
	
	public function checkSSL() {
		if($this->requireSSL == 1) {
			global $c;
			$cp = new Permissions($c);
			if (isset($cp)) {
				if (!$cp->canWrite() && !$cp->canAddSubContent() && !$cp->canAdminPage() && !$cp->canApproveCollection()) {	
					if($_SERVER['HTTPS']!="on") {
						$redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
						header("Location:$redirect");
					}
				}
			}
		}
	}
	
	public function loadHeaderItems($fID = 0) {
		$uh = Loader::helper('concrete/urls');
		$html = Loader::helper('html');
		$headerItems = array();
		
		if($fID == 0) {
			$f = \Concrete\Package\Formify\Src\FormifyForm::get($this->fID);
		} else {
			$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		}
		
		if(is_object($f)) {
			
			//Output field header items
			foreach($f->getFields() as $ff) {
				$ft = $ff->getType();
				foreach($ft->getHeaderItems() as $item) {
					if($item['type'] == 'javascript') {
						$headerItems[] = $html->javascript($item['file'],$item['package']);
					} else {
						$headerItems[] = $html->css($item['file'],$item['package']);
					}
				}
				
				if(file_exists(DIR_PACKAGES . '/formify/css/field_types/' . $ft->handle . '.css')) {
					$headerItems[] = $html->css('field_types/' . $ft->handle . '.css','formify');
				}
			}
			
			//Output integration header items
			foreach($f->getActiveIntegrations() as $i) {
				
				foreach($i->getHeaderItems() as $item) {
					if($item['type'] == 'javascript') {
						$headerItems[] = $html->javascript($item['file'],$item['package']);
					} else {
						$headerItems[] = $html->css($item['file'],$item['package']);
					}
				}
			}
		}
		
		// add recaptcha to header
		$headerItems[] = $html->javascript("https://www.google.com/recaptcha/api.js?render=explicit");

		foreach($headerItems as $item) {
			$this->addHeaderItem($item);
		}
	}
	
	public function loadFooterItems($fID = 0) {
		$uh = Loader::helper('concrete/urls');
		$html = Loader::helper('html');
		$footerItems = array();
		
		if($fID == 0) {
			$f = \Concrete\Package\Formify\Src\FormifyForm::get($this->fID);
		} else {
			$f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
		}
		
		if(is_object($f)) {
			
			//Output field footer items
			foreach($f->getFields() as $ff) {
				$ft = $ff->getType();
				foreach($ft->getFooterItems() as $item) {
					if($item['type'] == 'javascript') {
						$footerItems[] = $html->javascript($item['file'],$item['package']);
					} else {
						$footerItems[] = $html->css($item['file'],$item['package']);
					}
				}
			}
			
			//Output integration footer items
			foreach($f->getActiveIntegrations() as $i) {
				
				foreach($i->getFooterItems() as $item) {
					if($item['type'] == 'javascript') {
						$footerItems[] = $html->javascript($item['file'],$item['package']);
					} else {
						$footerItems[] = $html->css($item['file'],$item['package']);
					}
				}
				
				if(file_exists(DIR_PACKAGES . '/formify/js/integrations/' . strtolower($i->handle) . '.js')) {
					$footerItems[] = $html->javascript('integrations/' . strtolower($i->handle) . '.js','formify');
				}
			}
		}
		
		foreach($footerItems as $item) {
			$this->addFooterItem($item);
		}
	}
	
}