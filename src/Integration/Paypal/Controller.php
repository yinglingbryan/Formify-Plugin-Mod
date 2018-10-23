<?php   
namespace Concrete\Package\Formify\Src\Integration\Paypal;

class Controller extends \Concrete\Package\Formify\Src\FormifyIntegration\Controller {
	
	public $submitAction = 'post';
	public $submitActionURL = 'https://www.paypal.com/cgi-bin/webscr';
	
	public function finalize($r) {
		$response = array();
		
		$response['action'] = $this->submitAction;
		$response['url'] = $this->submitActionURL;
		
		$postData = array();
		$postData['cmd'] = '_xclick';
		$postData['business'] = $this->getForm()->getIntegrationConfig('Paypal','email');
		$postData['currency_code'] = $this->getForm()->getIntegrationConfig('Paypal','currency');
		$postData['item_name'] = $this->getForm()->getIntegrationConfig('Paypal','description');
		$postData['invoice'] = $r->rID;
		$postData['amount'] = $r->amountCharged;
		
		$response['postData'] = $postData;
		
		return $response;
	}
	
}