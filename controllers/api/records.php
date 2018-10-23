<?php  
namespace Concrete\Package\Formify\Controller\Api;
	
use \Concrete\Package\Formify\Src\FormifyForm;
use \Concrete\Package\Formify\Src\FormifyField;
use \Concrete\Package\Formify\Src\FormifyRecord;
use \Concrete\Package\Formify\Src\FormifyRecordSet;
use Concrete\Core\Application\Service\Dashboard;
use Package;
use Loader;
use Controller;
use Page;
use Log;
use Core;

class Records extends Controller {
	
	protected function canAccess() {
	    $formifyPage = \Page::getByPath('/dashboard/formify');
    	$formifyPermissions = new \Permissions($formifyPage);
    	return $formifyPermissions->canRead();
    }
    
    public function validate($fID,$sectionIndex) {
	    
	    $errors = array(); // Response array
	    
	    $f = \Concrete\Package\Formify\Src\FormifyForm::get($fID);
	    $s = $f->getSection($sectionIndex);
	    
	    if(count($s->getFields()) > 0) {
		    foreach($s->getFields() as $ff) {
			    if(!$ff->validate($_POST[$ff->ffID])) {
					$e = array();
					$e['type'] = 'validation';
					$e['ffID'] = $ff->ffID;
					$e['label'] = $ff->label;
					$e['message'] = $f->errorValidation;
					$errors[] = $e;
				}
		    }
	    }
	    
	    $json = Loader::helper('json');
		$rJSON = $json->encode($errors);
		echo $rJSON;
	    
    }
	
	public function process($id='') {
		
		$editing = false;
		$rID = $_POST['rID']; // Record ID
		$token = $_POST['token']; // Record Token
		$source = $_POST['source'];
		$referrer = $_POST['referrer'];
		
		/*
			1. Get/Create form
			2. Create fields (if form is in learn more and fields do not exist)
			3. Verify permissions
			4. Validate fields
			5. Get/Create record
			6. Save answers
			7. Calculate commerce total
			8. Send notifications
			9. Process integrations
			10. Prepare response
			11. Finalize integrations
			12. Return response
			
			Response JSON Object Format:
			{
				errors [
					{
						type,
						ffID,
						label,
						message
					}
				],
				amountCharged,
				rID,
				action,
				message,
				url
			}
			
		*/		
		
		$response = array(); // Response array
		$response['errors'] = array();
		
		/* 1. Setup Form */
		
		if($id) {
			$f = \Concrete\Package\Formify\Src\FormifyForm::get($id);
		} 
		
		if(!is_object($f)) {
			$pkg = Package::getByHandle('formify');
			$isMagic = $pkg->getConfig()->get('formify.magic');
			if($isMagic) {
				$f = \Concrete\Package\Formify\Src\FormifyForm::create(ucwords(strtolower($id)));
				$f->set('handle',$id);
				$f->set('magic',true);
			}
		}
		
		if(is_object($f)) {
			
			/* 2. Setup Fields */
			
			$fields = $f->getFields();
			$answers = array();
			
			foreach($fields as $ff) {
				
				if( $ff->type == 'textarea' ){
					$answers[$ff->ffID] = htmlentities($_POST[$ff->ffID]);
				} else {
					$answers[$ff->ffID] = $_POST[$ff->ffID];
				}
					
			}
			
			if($f->isMagic()) {
				foreach($_POST as $h => $v) {
					if(($h != 'rID') && ($h != 'token')) {
						
						$field = $f->getField($h);
						
						if(!$field) {
							$field = $f->addField();
							$field->set('label',ucfirst($h));
							$field->set('handle',$h);
							$fields[] = $field;
							$answers[$field->ffID] = $v;
						}
					}
				}
			}
			
			/* 3. Verify permissions */
			
			if(($rID != '') && ($token != '')) {
				$r = \Concrete\Package\Formify\Src\FormifyRecord::getWithToken($rID,$token);
				
				if(is_object($r)) {
					$editing = true;
				}
				
				if(!$r->userCanEdit()) {
					$e = array();
					$e['type'] = 'permissions';
					$e['message'] = $f->errorPermission;
					$response['errors'][] = $e;
				}
			} else {
				if(($f->getRecordCount() > $f->maxSubmissions) && ($f->maxSubmissions > 0)) {
					$e = array();
					$e['type'] = 'submissions';
					$e['message'] = $f->errorSubmissions;
					$response['errors'][] = $e;
				}
				
				if(!$f->userCanAdd()) {
					$e = array();
					$e['type'] = 'permissions';
					$e['message'] = $f->errorPermission;
					$response['errors'][] = $e;
				}
			}
			
			if((count($response['errors']) == 0) && (count($fields) > 0)) {
				
				/* 4. Validate Fields */


				/* 4.1a Validate Invisible reCAPTCHA */


				$grecaptcha = $f->grecaptcha;
				$grecaptcha_site_key = trim($f->grecaptchaSiteKey);
				$grecaptcha_secret_key = trim($f->grecaptchaSecretKey);
				if (strlen($grecaptcha_site_key) != 40) $grecaptcha = 0;
				if (strlen($grecaptcha_secret_key) != 40) $grecaptcha = 0;
				

				if ($grecaptcha == 1) {					

				$grc_response = $_POST['rcresponse'];
				$grc_userip = $_SERVER['REMOTE_ADDR'];

				$url = "https://www.google.com/recaptcha/api/siteverify?secret=".$grecaptcha_secret_key."&response=".$grc_response;
				$curl = curl_init();
		        curl_setopt($curl, CURLOPT_URL, $url);
		        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE); 
		        $curlData = curl_exec($curl);
		        curl_close($curl);
		        $res = json_decode($curlData, TRUE);
			    if($res['success'] != 'true') {
			    	$e = array();
					$e['type'] = 'validation';
					//$e['message'] = $f->errorCaptcha;
					$errorCodes = implode(",",$res["error-codes"]);
					$e['message'] = "Google reCAPTCHA failed : ".$errorCodes;
					$response['errors'][] = $e;
			    	}

			    }

				
				/* 4.1b Validate CAPTCHA */
				if ($f->captcha) {
		            $captcha = Core::make('helper/validation/captcha');
		            if (!$captcha->check()) {
			            $e = array();
						$e['type'] = 'validation';
						$e['message'] = $f->errorCaptcha;
						$response['errors'][] = $e;
		            }
		        }
		        
		        /* 4.2 Validate integrations */
		        $integrations = $f->getActiveIntegrations();
		        $validIntegrations = array();
				foreach($integrations as $i) {
					if($i->validate($f)) {
						$validIntegrations[] = $i;
					} else {
						$e = array();
						$e['type'] = 'validation';
						$e['message'] = $i->getValidationError();
						$response['errors'][] = $e;
					}
				}
				
				/* 4.3 Validate fields */
				foreach($fields as $ff) { //Loop through fields
					if(!$ff->validate($answers[$ff->ffID])) {
						$e = array();
						$e['type'] = 'validation';
						$e['ffID'] = $ff->ffID;
						$e['label'] = $ff->label;
						$e['message'] = $f->errorValidation;
						$response['errors'][] = $e;
					}
					
					// MGH Spam Prevention
					if( $ff->handle == "mgh-pot" ){
						if( $answers[$ff->ffID] != "" ){
							$e = array();
							$e['type'] = 'validation';
							$e['ffID'] = $ff->ffID;
							$e['label'] = $ff->label;
							$e['message'] = 'Spam detected, please try again!';
							$response['errors'][] = $e;
						}
					}
				}
				
				if(count($response['errors']) == 0) {
					
					/* 5. Get/Create record */
					if(is_object($r)) {
						$r->clear();
					} else {
						$r = $f->addRecord();
						$r->set('source',$source);
						$r->set('referrer',$referrer);
					
						switch($f->defaultRecordStatus) {
							case 1:
								$r->approve(true);
								break;
							case -1:
								$r->reject(true);
								break;
						}
					}
					
					$amountCharged = 0;
					
					/* 6. Save answers */
					foreach($fields as $field) {
						$r->addAnswer($field,$answers[$field->ffID]);
						
						if($field->getType()->handle == 'product') {
							
							if(floatval($field->price) == 0) {
								$fieldAmountCharged = $answers[$field->ffID];
							} else {
								$fieldAmountCharged = $field->price * $answers[$field->ffID];
							}
						}
						
						$amountCharged += $fieldAmountCharged;
						
					}
					
					/* 7. Calculate commerce total */
					$r->set('amountCharged',$amountCharged);
					
					
					$r->index();
					
					/* 8. Send notifications */
					if($editing) {
						//Send update notifications
						foreach($f->getNotifications('update') as $n) {
							$n->send($r);	
						}
					} else {
						//Send add notifications
						foreach($f->getNotifications('add') as $n) {
							$n->send($r);	
						}
					}
					
					/* 9. Process integrations */
					foreach($validIntegrations as $i) {
						
						$processedIntegrations = array();
						
						if($i->process($r)) {
							$processedIntegrations[] = $i;
						} else {
							$e = array();
							$e['type'] = 'integration';
							$e['integration'] = $i->name;
							$e['message'] = $i->getValidationError();
							$response['errors'][] = $e;
						}
					}
					
					if(count($response['errors']) == 0) {
						$rID = $r->rID;
					} else {
						$r->delete();
					}
					
				}
				
			}
			
		} else {
			//Form not found
			$e = array();
			$e['type'] = 'unknown';
			$e['message'] = 'Form not found.';
			$response['errors'][] = $e;
		}
			
			/* 10. Prepare response */
			
			$response['rID'] = $rID;
					
			if(($f->submitAction == 'URL') || ($f->submitAction == 'cID')) {
				$response['action'] = 'redirect';
				
				if($f->submitAction == 'URL') {
					$response['url'] = $f->submitActionURL;
				} else {
					$redirectCollection = Page::getByID($f->submitActionCollectionID);
					$response['url'] = $redirectCollection->getCollectionLink();
				}
				
				if($f->submitActionPassRecordID) {
					$response['url'] .= '?' . $f->submitActionRecordIDParameter . '=' . $rID;
				}
				
			} else {
				$response['action'] = $f->submitAction;
				$response['message'] = $f->submitActionMessage;
			}
			
			/* 11. Finalize integrations */
			if(count($validIntegrations) > 0) {
				foreach($validIntegrations as $i) {
					
					$iData = $i->finalize($r);
					
					if(count($iData) > 0) {
  					foreach($iData as $key => $val) {
  						$response[$key] = $val;
  					}
  				}
					
				}
			}
		
		
		
		if($_GET['ajax'] == 1) {
			
			/* 12. Return response */
			$json = Loader::helper('json');
			$rJSON = $json->encode($response);
			echo $rJSON;
			
		} else {
			//No ajax
			echo '<pre>';
			print_r($response);
			echo '</pre>';
		}
	}
	
	public function one($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
		$this->outputJSON($r);
	}
	
	public function rebuild($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
        $r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
        $r->index();
        $this->outputJSON($r);
	}
	
	public function all($fID,$page=1,$pageSize=25,$query='') {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$rs = \Concrete\Package\Formify\Src\FormifyRecordSet::get($fID);
		if($query != '') {
			$rs->setSearchQuery($query);
		}
		
		$rs->setPage($page);
		$rs->setPageSize($pageSize);
		
		$this->outputJSON($rs->getRecords());
	}
	
	public function delete($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
		$r->delete();
	}
	
	public function update($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$json = Loader::helper('json');
		$rJSON = $json->decode($json->encode($_POST));
		$r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
		foreach($rJSON as $property => $value) {
			$r->set($property,$value);
		}
	}
	
	public function getNext() {
		
	}
	
	public function getPrevious() {
		
	}
	
	public function resort($rID,$adjacentRecordID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
		$r->resort($adjacentRecordID);
	}
	
	public function approve($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
		$r->approve();
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($r->fID);
		foreach($f->getNotifications('approve') as $n) {
			$n->send($r);	
		}
	}
	
	public function reject($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
		$r->reject();
		
		$f = \Concrete\Package\Formify\Src\FormifyForm::get($r->fID);
		foreach($f->getNotifications('reject') as $n) {
			$n->send($r);	
		}
	}
	
	public function pend($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
		$r->pend();
	}
	
	public function migrate($rID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$r = \Concrete\Package\Formify\Src\FormifyRecord::get($rID);
		$r->migrate();
	}
	
	public function index($fID) {
		
		if (!$this->canAccess()) {
        	die(t('Access Denied'));
        }
        
		$rs = \Concrete\Package\Formify\Src\FormifyRecordSet::get($fID);
		$rs->setPageSize(0);
		foreach($rs->getRecords() as $r) {
			$r->index();
		}
	}
	
	protected function outputJSON($data,$debug = false) {
		
		$json = Loader::helper('json');
		
		if(($debug) || ($_GET['debug'] == 1)) {
			echo '<pre>';
			echo json_encode($data, JSON_PRETTY_PRINT);
			echo '</pre>';
		} else {
			header('Content-Type: application/json');
			echo $json->encode($data);
		}
	}
	
}