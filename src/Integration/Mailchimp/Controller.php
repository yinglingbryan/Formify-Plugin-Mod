<?php   
namespace Concrete\Package\Formify\Src\Integration\Mailchimp;

class Controller extends \Concrete\Package\Formify\Src\FormifyIntegration\Controller {
	
	public function add($r) {
		$apiKey = $this->getApiKey();
		$listID = $this->getListID();
		$email = $this->getEmail();
		
		if($apiKey != '') {
			$mc = new \Mailchimp($apiKey);
			if(($email != '') && ($listID != '')) {
				$result = $mc->call('lists/subscribe', array(
				    'id'                => $listID,
				    'email'             => array('email'=>$email),
				));
			}
		}
		
		return true;
	}
	
	public function getApiKey() {
		return $this->getForm()->getIntegrationConfig('Mailchimp','api_key');
	}
	
	public function getListID() {
		return $this->getForm()->getIntegrationConfig('Mailchimp','list_id');
	}
	
	public function getEmail() {
		$r = $this->getRecord();
		foreach($this->getForm()->getFields() as $ff) {
			if($ff->getIntegrationConfig('Mailchimp','mailchimp_value') == 'email') {
				return $r->getAnswerValue($ff->ffID);
			}
		}
	}
	
}