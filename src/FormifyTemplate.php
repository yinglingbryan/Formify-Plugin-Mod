<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_OPTIONS','FormifyOptions');
define('TABLE_FORMIFY_RECORDS','FormifyRecords');
define('TABLE_FORMIFY_ANSWERS','FormifyAnswers');
define('TABLE_FORMIFY_TEMPLATES','FormifyTemplates');

use \Concrete\Package\Formify\Src\FormifyForm;	
use \Concrete\Package\Formify\Src\FormifyField;	
use \Concrete\Package\Formify\Src\FormifyFieldType;	
use User;
use UserInfo;
use Loader;
use Package;
use Page;
use Log;
use File;

class FormifyTemplate {
	
	private $assignableProperties = array(
		'tID',
		'fID',
		'name',
		'type',
		'header',
		'content',
		'footer',
		'empty'
	);
	
	public function get($id) {
		if(is_numeric($id)) {
			$t = self::getByID($id);
		} else {
			$t = self::getByHandle($id);
		}
		
		return $t;
	}
	
	public function getByID($tID) {
		$db = Loader::db();
		$t = new self;
		
		$tData = $db->getRow("SELECT * FROM " . TABLE_FORMIFY_TEMPLATES . " WHERE tID = ?",array($tID));
		
		if (($tData['tID'] == $tID) && ($tID != 0)) {
			foreach($tData as $col => $val) {
				$t->$col = $val;
			}
			
			$t->placeholders = $t->getAvailablePlaceholders();
			
			return $t;
		} else {
			return false;
		}
	}
	
	public function getByHandle($handle) {
		$path = DIR_PACKAGES . '/formify/elements/templates/' . $handle;
		if(file_exists($path)) {
			$txt = Loader::helper('text');
			$t = new self;
			$t->tID = $handle;
			$t->type = 'list';
			$t->handle = $handle;
			$t->name = $txt->unhandle($handle);
			$t->header = @file_get_contents($path . '/header.html');
			$t->header = ($t->header ? $t->header : '');
			$t->content = @file_get_contents($path . '/content.html');
			$t->content = ($t->content ? $t->content : '');
			$t->footer = @file_get_contents($path . '/footer.html');
			$t->footer = ($t->footer ? $t->footer : '');
			$t->empty = @file_get_contents($path . '/empty.html');
			$t->empty = ($t->empty ? $t->empty : '');
			$t->isFile = true;
			return $t;
		} else {
			return false;
		}
	}
	
	public function all() {
		$db = Loader::db();
		$tData = $db->getAll("SELECT tID FROM " . TABLE_FORMIFY_TEMPLATES . "");
		$templates = array();
		if(count($tData) > 0) {
			foreach($tData as $tRow) {
				$templates[] = self::get($tRow['tID']);
			}
		}
		
		$fh = Loader::helper('file');
		$files = array();
		$path = DIR_PACKAGES . '/formify/elements/templates/';
		if (file_exists($path)) {
			$templateDirs = $fh->getDirectoryContents($path);
			if(count($templateDirs) > 0) {
				foreach($templateDirs as $td) {
					$templates[] = self::get($td);
				}
			}
		}
		
		return $templates;
	}
	
	public function create($name) {
		$db = Loader::db();
		$db->execute("INSERT INTO " . TABLE_FORMIFY_TEMPLATES . " (tID) VALUES (0)");
		$tID = $db->Insert_ID();
		$t = self::get($tID);
		$t->set('name',$name);
		return $t;
	}
	
	public function delete() {
		$db = Loader::db();
		$db->execute("DELETE FROM " . TABLE_FORMIFY_TEMPLATES . " WHERE tID=?",array($this->tID));
	}
	
	public function propertyIsAssignable($property) {
		foreach($this->assignableProperties as $ap) {
			if($ap == $property) {
				return true;
			}	
		}
		return false;
	}
	
	public function setBlockID($bID) {
		$this->bID = $bID;
	}
	
	public function setDetailCollectionID($cID) {
		$this->detailCollectionID = $cID;
	}
	
	public function getAvailablePlaceholders() {
		if(count($this->placeholders) > 0) {
			return $this->placeholders;
		} else {
			$placeholders = array();
			
			$placeholders[] = array('label'=>t('Username'),'handle'=>'{{ user }}');
			$placeholders[] = array('label'=>t('User Email'),'handle'=>'{{ user.email }}');
			$placeholders[] = array('label'=>t('User ID'),'handle'=>'{{ user.id }}');
			$placeholders[] = array('label'=>t('Timestamp'),'handle'=>'{{ timestamp }}');
			$placeholders[] = array('label'=>t('IP Address'),'handle'=>'{{ ip }}');
			$placeholders[] = array('label'=>t('Record ID'),'handle'=>'{{ id }}');
			$placeholders[] = array('label'=>t('Detail URL'),'handle'=>'{{ detailurl }}');
			$placeholders[] = array('label'=>t('List URL'),'handle'=>'{{ listurl }}');
			$placeholders[] = array('label'=>t('Primary Field Value'),'handle'=>'{{ primary }}');
			
			if($f = \Concrete\Package\Formify\Src\FormifyForm::get($this->fID)) {
				foreach($f->getFields() as $ff) {
					$placeholders[] = array('label'=>$ff->label,'handle'=>'{{ ' . $ff->handle . ' }}');
				}
			}
			
			return $placeholders;
		}
	}
	
	public function set($property,$value) {
		$db = Loader::db();
		if($this->propertyIsAssignable($property)) {
			$db->replace(TABLE_FORMIFY_TEMPLATES,array('tID'=>$this->tID,$property=>$value),'tID');
		}
		$this->$property = $value;
	}
	
	public function setRecords($records) {
		$this->records = $records;
	}
	
	public function render($records = '',$return = false) {
		
		$this->setRecords($records);
		
		if((count($this->records) == 0) || ($this->records == '')) {
			$content = $this->empty;
		} elseif(!is_array($this->records)) {
			
			$content = '';
			
			$content = $this->parseSection($this->records,$this->content);
			$content = $this->header . $content . $this->footer;
			
		} else {
		
			$content = '';
		
			foreach($this->records as $r) {
				$content .= $this->parseSection($r,$this->content);
			}
			
			$content = $this->header . $content . $this->footer;
			
		}
		
		if($return) {
			return $content;
		} else {
			echo $content;
		}
	}
	
	public function parseSection($r,$section) {
		$section = $this->parseRepeats($r,$section);
		$section = $this->parseIfs($r,$section);
		$section = $this->parsePlaceholders($r,$section);
		
		return $section;
	}
	
	public function parseRepeats($r,$content) {
	
		if($content == '') {
			return $content;
		} else {
			
			$doc = new \DOMDocument();
			
			if (version_compare(PHP_VERSION, '5.4') >= 0) {
				$doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
			} else {
				$doc->loadHTML($content);
			}
			
			$xpath = new \DOMXPath($doc);
			$elements = $xpath->query("//*[@formify-repeat]");
					
			if (count($elements) > 0) {
				
				foreach ($elements as $element) {
					
					$attr = $element->getAttribute('formify-repeat');
					$attrParts = explode(' ',$attr);
					
					$cloned = $element->cloneNode(true);
					$cloned->removeAttribute('formify-repeat');
					
					$repeatHandle = $attrParts[0];
					
					
					$handles = array();
					if($attrParts[2] == 'answers') {
						if(count($r->getAnswers()) > 0) {
							foreach($r->getAnswers() as $answer) {
								$handles[] = $answer->handle;
							}
						}
					} elseif($attrParts[2] == 'emailableanswers') {
						if(count($r->getAnswers()) > 0) {
							foreach($r->getAnswers() as $answer) {
								if($answer->includeInEmail) {
									$handles[] = $answer->handle;
								}
							}
						}
					} else {
						if(is_array($r->getAnswer($attrParts[2])->value)) {
							foreach($r->getAnswer($attrParts[2])->value as $answer) {
								$handles[] = $attrParts[2];
							}
						}
					}
					
					
					$newDoc = new \DOMDocument();
					
					$i = 0;
						
					foreach($handles as $h) {
						
						$newElement = $newDoc->importNode($cloned,true);
						
						$translations = array();
						$translations[] = array(
							'repeatHandle' => $repeatHandle,
							'fieldHandle' => $h,
							'index' => $i
						);
						
		
						if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
							$content = $this->parsePlaceholders($r,$newDoc->saveHTML($newElement),$translations);
						} else {
							$content = $this->parsePlaceholders($r,$newDoc->saveXML($newElement),$translations);
						}
						
						if($content != '') {
							$newFrag = $newDoc->createDocumentFragment();
							$newFrag->appendXML($content);
							$newDoc->appendChild($newFrag);	
						}
						
						if(($attrParts[2] != 'answers') && ($attrParts[2] != 'emailableanswers')) {
							$i++;
						}
						
					}
					
					/*
					Changed at version 1.1.3
					*/
					$fragment = $doc->createDocumentFragment();	
					
					$parts = explode("\n", $newDoc->saveXML(), 2);
					$fragment->appendXML($parts[1]);
					$element->parentNode->replaceChild($fragment,$element);
					
				} // End loop through repeat elements
			}
			
			return $doc->saveHTML();
		
		}
	}
	
	public function parseIfs($r,$content) {
		
		if($content == '') {
			return $content;
		} else {
	
			$doc = new \DOMDocument();
			
			if (version_compare(PHP_VERSION, '5.4') >= 0) {
				$doc->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
			} else {
				$doc->loadHTML($content);
			}
			
			$xpath = new \DOMXPath($doc);
			$ifElements = $xpath->query("//*[@formify-if]");
			
			if (count($ifElements) > 0) {
				foreach ($ifElements as $ifElement) {
					
					$handle = $ifElement->getAttribute('formify-if');
					$comparison = $ifElement->getAttribute('formify-comparison');
					$value = $ifElement->getAttribute('formify-value');
					
					$keepNode = false;
					
					if($handle == 'status') {
						switch($value) {
							case 'approved':
								if($r->approval == '1') {
									$keepNode = true;
								}
								break;
							case 'rejected':
								if($r->approval == '-1') {
									$keepNode = true;
								}
								break;
							case 'pending':
								if($r->approval == '0') {
									$keepNode == false;
								}
								break;
						}
					} elseif (($comparison != '') && ($value != '')) {
						switch($comparison) {
							case '=':
								if($r->getAnswerValue($handle) == $value) {
									$keepNode = true;
								}
								break;
							case '==';
								if($r->getAnswerValue($handle) == $value) {
									$keepNode = true;
								}
								break;
							case '!=';
								if($r->getAnswerValue($handle) != $value) {
									$keepNode = true;
								}
								break;
							case '>';
								if($r->getAnswerValue($handle) > $value) {
									$keepNode = true;
								}
								break;
							case '>=';
								if($r->getAnswerValue($handle) >= $value) {
									$keepNode = true;
								}
								break;
							case '<';
								if($r->getAnswerValue($handle) < $value) {
									$keepNode = true;
								}
								break;
							case '<=';
								if($r->getAnswerValue($handle) <= $value) {
									$keepNode = true;
								}
								break;
						}
					} else {
						if($r->getAnswerValue($handle) != '') {
							$keepNode = true;
						}
					}
					
					if(!$keepNode) {
						$ifElement->parentNode->removeChild($ifElement);
					}
					
				}
			}
			
			return $doc->saveHTML();
			
		}
		
	}
	
	public function parsePlaceholders($r,$content,$translations='') {
		$placeholders = $this->getPlaceholders($r,$content,$translations);
		
		$content = str_replace('%7B', '{', $content);
		$content = str_replace('%7D', '}', $content);
		$content = str_replace('%20', ' ', $content);
		$content = str_replace('%5B', '[', $content);
		$content = str_replace('%5D', ']', $content);
		
		foreach($placeholders as $p) {
			$content = str_replace($p['text'],$p['value'],$content);
		}
		
		/*
		$content = html_entity_decode($content);
		$content = utf8_encode($content);
		*/
		
		return $content;
		
	}
	
	public function getPlaceholders($r,$content,$translations='') {
		
		$content = str_replace('%7B', '{', $content);
		$content = str_replace('%7D', '}', $content);
		$content = str_replace('%20', ' ', $content);
		$content = str_replace('%5B', '[', $content);
		$content = str_replace('%5D', ']', $content);
		
		preg_match_all("~{{.*?\}}~", $content, $matches);
		
		$placeholders = array();
		
		foreach($matches[0] as $m) {
			
			$placeholder = array();
			
			$phParts = explode('.',str_replace(array('{{','}}'),'',$m));
			
			$nameParts = explode('[',$phParts[0]);
			$name = $nameParts[0];
			$property = $phParts[1];
			
			$placeholder['text'] = $m;
			$placeholder['name'] = trim($name);
			$placeholder['index'] = 0;
			
			if(is_array($translations)) {
				if(count($translations) > 0) {
					foreach($translations as $t) {
						if($t['repeatHandle'] == $placeholder['name']) {
							$placeholder['name'] = $t['fieldHandle'];
							$placeholder['index'] = $t['index'];
						}
					}
				}
			}
			
			$placeholder['property'] = trim($property);
			if($placeholder['property'] == '') {
				$placeholder['property'] == 'value';
			}
			
			preg_match_all("~\[.*?\]~", $m, $attributes);
			
			foreach($attributes[0] as $a) {
				$attribute = array();
				
				preg_match_all('~(["\'])([^"\']+)\1~', $a, $aPairs);
				
				
				foreach($aPairs[0] as $value) {
					$handle = str_replace($value,'',$a);
					$handle = str_replace(array('[',':',']'),'',$handle);
					
					$attribute['handle'] = trim($handle);
					$attribute['value'] = trim(trim($value,'"\''));
				}
				
				$placeholder['attributes'][] = $attribute;
			}
			
			switch($placeholder['name']) {
				case 'user';
					switch ($placeholder['property']) {
						case 'username':
							$placeholder['value'] = $r->username;
							break;
						case 'id':
							$placeholder['value'] = $r->uID;
							break;
						case 'email':
							$placeholder['value'] = $r->email;
							break;
						default:
							if($placeholder['property'] == '') {
								$placeholder['value'] = $r->username;
							} else {
								if($u = UserInfo::getByID($r->uID)) {
									$placeholder['value'] = $u->getAttribute($placeholder['property']);
								}
							}
							break;
					}
					break;
				case 'timestamp';
					
					$format = 'F j, Y';
					
					if(count($placeholder['attributes']) > 0) {
						foreach($placeholder['attributes'] as $pa) {
							if($pa['handle'] == 'format') {
								$format = $pa['value'];
							}
						}
					}
					
					$placeholder['value'] = date($format,$r->created / 1000);
					break;
				case 'status';
					$placeholder['value'] = $r->status;
					break;
				case 'id';
					$placeholder['value'] = $r->rID;
					break;
				case 'ip';
					$placeholder['value'] = $r->ipAddress;
					break;
				case 'amountcharged';
					$placeholder['value'] = $r->amountCharged;
					break;
				case 'amountpaid';
					$placeholder['value'] = $r->amountPaid;
					break;
				case 'primary';
					$placeholder['value'] = $r->name;
					break;
				case 'source';
					$placeholder['value'] = $r->source;
					break;
				case 'referrer';
					$placeholder['value'] = $r->referrer;
					break;
				case 'detailurl';
					if(intval($this->detailCollectionID) == 0) {
						$detailPage = Page::getCurrentPage();
					} else {
						$detailPage = Page::getByID($this->detailCollectionID);
					}
					if($detailPage) {
						$placeholder['value'] = $detailPage->getCollectionLink() . '?rID[' . $this->bID . ']=' . $r->rID;
					}
					break;
				case 'listurl';
					$listPage = Page::getCurrentPage();
					$placeholder['value'] = $listPage->getCollectionLink();
					break;
				case 'url';
					if($placeholder['attributes'][0]['handle'] == 'cID') {
						$placeholder['value'] = Page::getByID($placeholder['attributes'][0]['value'])->getCollectionLink();
					}
					if($placeholder['attributes'][0]['handle'] == 'fID') {
						$file = File::getByID($placeholder['attributes'][0]['value']);
						$placeholder['value'] = $file->getApprovedVersion()->getURL();
					}
					break;
				default:
					switch ($placeholder['property']) {
						case 'label':
							$placeholder['value'] = htmlentities($r->getAnswer($placeholder['name'])->label);
							break;
						case 'url':
							$fileID = intval($r->getAnswerValue($placeholder['name'],$placeholder['index']));
							if($fileID) {
								$file = File::getByID($r->getAnswerValue($placeholder['name'],$placeholder['index']));
								$placeholder['value'] = $file->getApprovedVersion()->getURL();
							}
							break;
						default:
							$placeholder['value'] = htmlentities($r->getFriendlyAnswerValue($placeholder['name'],$placeholder['index']));
							break;
					}
					
					if(count($placeholder['attributes']) > 0) {
						
						foreach($placeholder['attributes'] as $pa) {
							
							if($pa['handle'] == 'format') {
								$placeholder['value'] = \DateTime::createFromFormat('U',$r->getAnswerValue($placeholder['name']))->format($pa['value']);
							}
							
							if(($pa['handle'] == 'html') && ($pa['value'] == 'true')) {
								$placeholder['value'] = html_entity_decode($placeholder['value']);
							}
								
						}
					}
					
					
			}
			
			$placeholders[] = $placeholder;
		}
		
		
		return $placeholders;	
	}

	
}