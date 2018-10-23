<?php  
namespace Concrete\Package\Formify\Controller\SinglePage\Dashboard\Formify\Forms;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Core\Attribute\Key\UserKey;
use \Concrete\Core\File\Set\SetList as FileSetList;
use FileSet;
use Session;
use Loader;

defined('C5_EXECUTE') or die("Access Denied.");

class Fields extends DashboardPageController {

	public function view($fID=0) {
		
		if($fID > 0) {
			$sessionFormID = $fID;
			Session::set('formifyFormID',$fID);
		} else {
			$sessionFormID = Session::get('formifyFormID');
		}
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($sessionFormID);
		$this->set('f',$f);
		
		$userAttributes = UserKey::getList();
		$this->set('userAttributes',$userAttributes);
		
		$fsl = new FileSetList();
		$fsl->filterByType(FileSet::TYPE_PUBLIC);
		$fileSets = $fsl->getPage();
		$this->set('fileSets',$fileSets);
		
		if(!$f) {
			$this->redirect('/dashboard/formify/forms');
		}
		
		$html = Loader::helper('html');
		
		$this->addHeaderItem($html->css('formify.css','formify'));
		$this->addHeaderItem($html->css('fields.css','formify'));
		
		$this->addFooterItem($html->javascript('angular.min.js','formify'));
        $this->addFooterItem($html->javascript('draganddrop.js','formify'));
		$this->addFooterItem($html->javascript('ui-bootstrap-custom-tpls-0.13.0.min.js','formify'));
		$this->addFooterItem($html->javascript('formify.js','formify'));
		$this->addFooterItem($html->javascript('fields.js','formify'));
        $this->addFooterItem($html->javascript('controllers/fields.js','formify'));
	}
}