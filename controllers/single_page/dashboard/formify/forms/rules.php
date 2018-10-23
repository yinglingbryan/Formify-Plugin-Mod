<?php  
namespace Concrete\Package\Formify\Controller\SinglePage\Dashboard\Formify\Forms;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\Formify\Src\FormifyForm;
use Session;
use Loader;

defined('C5_EXECUTE') or die("Access Denied.");

class Rules extends DashboardPageController {

	public function view($fID=0) {
		
		if($fID > 0) {
			$sessionFormID = $fID;
			Session::set('formifyFormID',$fID);
		} else {
			$sessionFormID = Session::get('formifyFormID');
		}
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($sessionFormID);
		$this->set('f',$f);
		
		if(!$f) {
			$this->redirect('/dashboard/formify/forms');
		}
		
		$html = Loader::helper('html');
		
		$this->addHeaderItem($html->css('formify.css','formify'));
		$this->addHeaderItem($html->css('rules.css','formify'));
		
		$this->addFooterItem($html->javascript('angular.min.js','formify'));
        $this->addFooterItem($html->javascript('draganddrop.js','formify'));
		$this->addFooterItem($html->javascript('ui-bootstrap-custom-tpls-0.13.0.min.js','formify'));
		$this->addFooterItem($html->javascript('formify.js','formify'));
		$this->addFooterItem($html->javascript('rules.js','formify'));
        $this->addFooterItem($html->javascript('controllers/rules.js','formify'));
	}
}