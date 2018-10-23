<?php      
namespace Concrete\Package\Formify\Block\FormifyView;

use \Concrete\Core\Block\BlockController;
use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyTemplate;
use Loader;
use Page;
use Log;


class Controller extends BlockController {

	protected $btTable = 'btFormifyView';
	protected $btInterfaceWidth = "450";
	protected $btInterfaceHeight = "380";
    protected $btDefaultSet = 'formify';

	public function getBlockTypeDescription() {
		return t("Render Formify data within a template");
	}
	
	public function getBlockTypeName() {
		return t("Formify View");
	}
	
	public function view() {
		
		if($r = \Concrete\Package\Formify\Src\FormifyRecord::get($_GET['rID'][$this->bID])) {
			$records[] = $r;
			$template = \Concrete\Package\Formify\Src\FormifyTemplate::get($this->detailTemplateID);
		} elseif($r = \Concrete\Package\Formify\Src\FormifyRecord::get($_GET['rID'][0])) {
			$records[] = $r;
			$template = \Concrete\Package\Formify\Src\FormifyTemplate::get($this->detailTemplateID);
		} elseif($rs = \Concrete\Package\Formify\Src\FormifyRecordSet::get($this->fID)) {
			
			if($this->requireApproval) {
				$rs->requireApproval();
			}
			
			if($this->requireOwnership) {
				$rs->requireOwnership();
			}
			
			if($this->sortBy) {
				$rs->setSortField($this->sortBy);
			}
			
			if($this->sortOrder != '') {	
				$rs->setSortOrder($this->sortOrder);
			}
			
			if($this->query != '') {
				$this->set('query',$this->query);
				$rs->setQuery($this->query);
			}
			
			if ($_GET['ccm_paging_p'] == '') {
				$rs->setPage(1);
			} else {
				$rs->setPage(intval($_GET['ccm_paging_p']));
			}
			
			$rs->setPageSize(intval($this->pageSize));
			
			if($this->includeExpired) {
				$rs->includeExpired();
			}
			
			$records = $rs->getRecords();
			$template = \Concrete\Package\Formify\Src\FormifyTemplate::get($this->listTemplateID);
			
		}
		
		if($template) {
			if(($this->detailDestination == 'page') && (intval($this->detailCID) > 0)) {
				$template->setDetailCollectionID($this->detailCID);
				$template->setBlockID(0);
			} else {
				$template->setBlockID($this->bID);
			}
		}
		
		if(($records) && ($template)) {
			$this->set('records',$records);
			$this->set('template',$template);
		}
		
		if(($this->displayPaginator) && (intval($this->pageSize) > 0) && ($rs)) {
			$fullRecordSet = $rs;
			$fullRecordSet->setPageSize(0);
			$fullRecordSet->clearRecords();
			$allRecords = $fullRecordSet->getRecords();
			if(count($allRecords) > count($records)) {
				$pageBase = Page::getCurrentPage()->getCollectionLink();
				$paginator = $this->createPagination($pageBase,count($allRecords),$this->pageSize,$rs->getPage());
				$this->set('paginator',$paginator);
			}
		}
		
		$this->set('c',Page::getCurrentPage());
	}
	
	public function add() {
        $this->edit();
    }
	
	public function edit() {
		$forms = \Concrete\Package\Formify\Src\FormifyForm::getAll();
		
		$this->set('forms',$forms);
			
		foreach($forms as $form) {
			if($form->fID == $this->fID) {
				$f = $form;
				$this->set('f',$f);
			}
		}
		
		$templates = \Concrete\Package\Formify\Src\FormifyTemplate::all();
		$this->set('templates',$templates);
	}
	
	public function save($data) {
		
		$json = Loader::helper('json');
		$data['sortableFields'] = $json->encode($data['sortableFields']);
		
		parent::save($data);
	}
	
	public function createPagination($pageBase,$numItems,$itemsPerPage,$currentPage) {
		$paginator=Loader::helper('pagination');
		if(intval($itemsPerPage) == 0) {
			$paginatorPageSize = 100000000;
		} else {
			$paginatorPageSize = $itemsPerPage;
		}
		$paginator->init(intval($currentPage),$numItems,$pageBase,$paginatorPageSize);
		return $paginator;
	}
	
	public function action_detail($rID) {
		$this->view();
	}
	
	public function action_search() {
		$this->query = $_POST['q'];
		$this->view();
	}
	
}