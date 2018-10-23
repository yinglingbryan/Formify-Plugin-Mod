<?php  
namespace Concrete\Package\Formify\Controller\SinglePage\Dashboard\Formify;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\Formify\Src\FormifyForm;
use Loader;

defined('C5_EXECUTE') or die("Access Denied.");

class Forms extends DashboardPageController {

	public function on_start()
    {
        $html = Loader::helper('html');
        
        $this->addHeaderItem($html->css('formify.css','formify'));
        $this->addHeaderItem($html->css('forms.css','formify'));
        
        $this->addFooterItem($html->javascript('angular.min.js','formify'));
        $this->addFooterItem($html->javascript('draganddrop.js','formify'));
        $this->addFooterItem($html->javascript('ui-bootstrap-custom-tpls-0.13.0.min.js','formify'));
        $this->addFooterItem($html->javascript('formify.js','formify'));
        $this->addFooterItem($html->javascript('forms.js','formify'));
        $this->addFooterItem($html->javascript('controllers/forms.js','formify'));
    }


	public function view()
	{
		$forms = \Concrete\Package\Formify\Src\FormifyForm::getAll();
		$this->set('forms',$forms);
	}

}