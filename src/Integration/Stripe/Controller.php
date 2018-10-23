<?php   
namespace Concrete\Package\Formify\Src\Integration\Stripe;

use \Stripe\Error\Card;

class Controller extends \Concrete\Package\Formify\Src\FormifyIntegration\Controller {
	
	public function add($r) {
		
		if($r->amountCharged > 0) {
			
			if($this->hasToken()) {
				
				try {
					$charge = \Stripe\Charge::create(array(
						"amount" => $r->amountCharged * 100, // Amount in cents
						"currency" => "usd",
						"source" => $this->getToken(),
						"description" => $this->getTransactionDescription() . ' - ' . $r->rID
					));
				} catch (\Stripe\Error\InvalidRequest $e) {
					$this->validationError = t('Invalid request.');
				} catch (\Stripe\Error\ApiConnection $e) {
				    // Network problem, perhaps try again.
				    $this->validationError = t('Network problem. Please try again');
				} catch (\Stripe\Error\Api $e) {
				    // Stripe's servers are down!
				    $this->validationError = t('API connection error.');
				} catch (\Stripe\Error\Card $e) {
				    // Card was declined.
				    $body = $e->getJsonBody();
					$err = $body['error'];
				    $this->validationError = $err['message'];
				}
				
				$r->set('amountPaid',$r->amountCharged);
				
				if($this->hasValidationError()) {
					return false;
				} else {
					return true;
				}
				
			} else {
				$this->validationError = 'Invalid card number.';
				return false;
			}
		
		} else {
			return true;
		}
		
	}
	
	public function validate($f) {
		
		$this->f = $f;
		
		$exp = explode('/',$_POST['stripe_expiration']);
		
		if(
			($_POST['stripe_card_num'] == '') ||
			($_POST['stripe_name'] == '') ||
			($exp[0] == '') ||
			($exp[1] == '') ||
			($_POST['stripe_code'] == '')
		) {
			$this->validationError = 'Credit card information missing.';
			return false;
		}
		
		if(!is_numeric($_POST['stripe_card_num'])) {
			$this->validationError = 'Invalid credit card number';
			return false;
		}
		
		if(!is_numeric($_POST['stripe_code'])) {
			$this->validationError = 'Invalid security code';
			return false;
		}
	
		\Stripe\Stripe::setApiKey($this->getSecretKey());
		
		try {
			$token = \Stripe\Token::create(array(
				"card" => array(
					"number" => $_POST['stripe_card_num'],
					"name" => $_POST['stripe_name'],
					"exp_month" => $exp[0],
					"exp_year" => $exp[1],
					"cvc" => $_POST['stripe_code']
				)
			));
		} catch (\Stripe\Error\Card $e) {
		    // Card was declined.
		    $body = $e->getJsonBody();
			$err = $body['error'];
		    $this->validationError = $err['message'];
		}
		
		$this->setToken($token->id);
		
		if($this->hasToken()) {
			return true;
		} else {
			return false;
		}
		
	}
	
	public function getSecretKey() {
		if($this->getMode() == 'live') {
			return $this->getForm()->getIntegrationConfig('Stripe','secret_key');
		} else {
			return $this->getForm()->getIntegrationConfig('Stripe','test_secret_key');
		}
	}
	
	public function getPublishableKey() {
		if($this->getMode() == 'live') {
			return $this->getForm()->getIntegrationConfig('Stripe','publishable_key');
		} else {
			return $this->getForm()->getIntegrationConfig('Stripe','test_publishable_key');
		}
	}
	
	public function getMode() {
		return $this->getForm()->getIntegrationConfig('Stripe','mode');
	}
	
	public function getTransactionDescription() {
		return $this->getForm()->getIntegrationConfig('Stripe','transaction_description');
	}
	
	public function hasValidationError() {
		if($this->validationError != '') {
			return true;
		} else {
			return false;
		}
	}
	
	public function setToken($token) {
		$this->token = $token;
	}
	
	public function getToken() {
		return $this->token;
	}
	
	public function hasToken() {
		if($this->token == '') {
			return false;
		} else {
			return true;
		}
	}
	
}