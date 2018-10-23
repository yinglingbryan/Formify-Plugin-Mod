<?php  
namespace Concrete\Package\Formify\Controller\SinglePage\Dashboard;
use \Concrete\Core\Page\Controller\DashboardPageController;

defined('C5_EXECUTE') or die("Access Denied.");

class Formify extends DashboardPageController {

	public function view() {
		$this->redirect('dashboard/formify/forms');
	}

}