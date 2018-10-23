<?php  
namespace Concrete\Package\Formify\Controller\SinglePage\Dashboard\Formify\Forms;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\Formify\Src\FormifyForm;
use Block;
use BlockType;
use Session;
use Loader;
use View;
use Page;
use Area;
use Stack;
use \Concrete\Controller\Search\Users as SearchUsersController;

defined('C5_EXECUTE') or die("Access Denied.");

class Records extends DashboardPageController {
	
	public function on_before_render() {
		/*
		$view = View::getInstance();
		
		$stack = Stack::getByName('Formify Backend');
        $this->set('stack',$stack);
        
        $blocks = $stack->getBlocks(STACKS_AREA_NAME);
        
        foreach($blocks as $b) {
			$btc = $b->getInstance();
			// now we inject any custom template CSS and JavaScript into the header
            if ($btc instanceof \Concrete\Core\Block\BlockController) {
				$btc->outputAutoHeaderItems();
			}
			$btc->runTask('on_page_view', array($view));
		}
        */ 
    
	}

	public function view($fID='') {
		
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
		$cnt = new SearchUsersController();
		$cnt->search();
        $this->set('searchController', $cnt);
		
		$this->addHeaderItem($html->css('formify.css','formify'));
		$this->addHeaderItem($html->css('records.css','formify'));
		
		$this->addFooterItem($html->javascript('angular.min.js','formify'));
        $this->addFooterItem($html->javascript('draganddrop.js','formify'));
		$this->addFooterItem($html->javascript('ui-bootstrap-custom-tpls-0.13.0.min.js','formify'));
		$this->addFooterItem($html->javascript('formify.js','formify'));
		$this->addFooterItem($html->javascript('records.js','formify'));
        $this->addFooterItem($html->javascript('controllers/records.js','formify'));
        
	}
	
	public function edit($fID,$rID='',$token='') {
		if($fID > 0) {
			$sessionFormID = $fID;
			Session::set('formifyFormID',$fID);
		} else {
			$sessionFormID = Session::get('formifyFormID');
		}
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($sessionFormID);
		$this->set('f',$f);
		$this->set('rID',$rID);
		$this->set('token',$token);
		
		if(!$f) {
			$this->redirect('/dashboard/formify/forms');
		}
		
		$html = Loader::helper('html');
		
		$this->addHeaderItem($html->css('formify.css','formify'));
		$this->addHeaderItem($html->css('records.css','formify'));
		
		$this->addFooterItem($html->javascript('angular.min.js','formify'));
    $this->addFooterItem($html->javascript('draganddrop.js','formify'));
		$this->addFooterItem($html->javascript('ui-bootstrap-custom-tpls-0.13.0.min.js','formify'));
		$this->addFooterItem($html->javascript('formify.js','formify'));
		$this->addFooterItem($html->javascript('records.js','formify'));
    $this->addFooterItem($html->javascript('controllers/records.js','formify'));
    
    $formifyBT = BlockType::getByHandle('formify_form');
    $this->addHeaderItem($html->css(DIR_REL . '/packages/formify/blocks/formify_form/view.css'));
    $this->addFooterItem($html->javascript(DIR_REL . '/packages/formify/blocks/formify_form/view.js'));
    $formifyBT->controller->on_page_view();
		
		$this->set('action','edit');

	}
	
}