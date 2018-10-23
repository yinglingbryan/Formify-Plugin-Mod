<?php   
namespace Concrete\Package\Formify\Src\FormifyIntegration;

use \Concrete\Package\Formify\Src\FormifyField;
use Core;
use Package;
use Loader;
use Log;	

class Integration {
	
	private $configXML;
	private $controller;
	
	public function all() {
		$pkg = Package::getByHandle('formify');
		$pkgPath = $pkg->getPackagePath();
		$iPath = $pkgPath . '/src/Integration';
		
		$fh = Loader::helper('file');
		$iFiles = $fh->getDirectoryContents($iPath);
		sort($iFiles);
		
		$integrations = array();
		
		if(count($iFiles) > 0) {
			foreach($iFiles as $iHandle) {
				$integrations[] = \Concrete\Package\Formify\Src\FormifyIntegration\Integration::get($iHandle);				
			}
		}
		
		return $integrations;
	}
	
	public function get($handle) {
		$i = new self;
		$i->handle = $handle;
		$i->loadConfig();
		
		$iHandle = Core::make('helper/text')->camelcase($handle);
        $class = '\\Concrete\\Package\\Formify\\Src\\Integration\\' . $iHandle . '\\Controller';
        $i->controller = new $class;
        
        $i->formConfigKeys = $i->getConfigKeys('form');
        $i->fieldConfigKeys = $i->getConfigKeys('field');
		
		return $i;
	}
	
	public function loadConfig() {
		$pkg = Package::getByHandle('formify');
		$pkgPath = $pkg->getPackagePath();
		$configFile = $pkgPath . '/src/Integration/' . $this->handle . '/config.xml';
		$configXML = simplexml_load_file($configFile);

		$this->configXML = $configXML;
		$this->name = (string) $configXML['name'];

		return $configXML;
	}
	
	public function getConfigKeys($type) {
		$keys = array();
		if(count($this->configXML->keys->key) > 0) {
			foreach($this->configXML->keys->key as $k) {
				$key = array();
				if((string) $k['context'] == $type) {
					$key['name'] = (string) $k['name'];
					$key['handle'] = (string) $k['handle'];
					$key['type'] = (string) $k['type'];
					
					if(count($k->options->option) > 0) {
						$options = array();
						foreach($k->options->option as $o) {
							$option = array();
							$option['label'] = (string) $o;
							$option['value'] = (string) $o['value'];
							
							$options[] = $option;
						}
					}
					
					$key['options'] = $options;
					
					$keys[] = $key;
				}
			}
		}
		return $keys;
	}
	
	public function getFormConfigKeys() {
		return $this->getConfigKeys('form');
	}
	
	public function getFieldConfigKeys() {
		return $this->getConfigKeys('field');
	}
	
	public function getHeaderItems() {
		$headerItems = array();
		if(count($this->configXML->headeritems) > 0) {
			foreach($this->configXML->headeritems->item as $hiXML) {
				$headerItem = array();
				$headerItem['type'] = (string) $hiXML['type'];
				$headerItem['file'] = (string) $hiXML['file'];
				$headerItem['package'] = (string) $hiXML['package'];
				$headerItems[] = $headerItem;
			}
		}
		return $headerItems;
	}
	
	public function getFooterItems() {
		$footerItems = array();
		if(count($this->configXML->footeritems) > 0) {
			foreach($this->configXML->footeritems->item as $fiXML) {
				$footerItem = array();
				$footerItem['type'] = (string) $fiXML['type'];
				$footerItem['file'] = (string) $fiXML['file'];
				$footerItem['package'] = (string) $fiXML['package'];
				$footerItems[] = $footerItem;
			}
		}
		return $footerItems;
	}
	
	public function getFields() {
		$fields = array();
		if(count($this->configXML->fields) > 0) {
			foreach($this->configXML->fields->field as $fieldXML) {
				
				$field = array();
				$field['type'] = (string) $fieldXML['type'];
				$field['label'] = (string) $fieldXML['label'];
				$field['name'] = (string) $fieldXML['name'];
				$field['required'] = (string) $fieldXML['required'];
				$field['placeholder'] = (string) $fieldXML['placeholder'];
				
				if(count($fieldXML->options->option) > 0) {
					$options = array();
					foreach($fieldXML->options->option as $o) {
						$option = array();
						$option['label'] = (string) $o;
						$option['value'] = (string) $o['value'];
						
						$options[] = $option;
					}
				}
				
				$field['options'] = $options;
				
				$ff = \Concrete\Package\Formify\Src\FormifyField::get($field);
				
				$fields[] = $ff;
			}
		}
		return $fields;
	}
	
	public function validate($f) {
		return $this->controller->validate($f);
	}
	
	public function process($record) {
		return $this->controller->process($record);
	}
	
	public function finalize($record) {
		return $this->controller->finalize($record);
	}
	
	public function getValidationError() {
		return $this->controller->validationError;
	}
	
}