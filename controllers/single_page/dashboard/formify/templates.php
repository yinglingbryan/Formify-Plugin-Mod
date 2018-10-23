<?php  
namespace Concrete\Package\Formify\Controller\SinglePage\Dashboard\Formify;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\Formify\Src\FormifyForm;
use Session;
use Loader;
use View;

defined('C5_EXECUTE') or die("Access Denied.");

class Templates extends DashboardPageController {

	public function view() {
		
		$html = Loader::helper('html');
		$v = View::getInstance();
        $v->requireAsset('core/file-manager');

		$this->addHeaderItem($html->css('formify.css','formify'));
		$this->addHeaderItem($html->css('templates.css','formify'));
		
		$this->addFooterItem($html->javascript('angular.min.js','formify'));
        $this->addFooterItem($html->javascript('draganddrop.js','formify'));
		$this->addFooterItem($html->javascript('ui-bootstrap-custom-tpls-0.13.0.min.js','formify'));
		$this->addFooterItem($html->javascript('formify.js','formify'));
		$this->addFooterItem($html->javascript('templates.js','formify'));
		$this->addFooterItem($html->javascript('controllers/templates.js','formify'));
	}
}