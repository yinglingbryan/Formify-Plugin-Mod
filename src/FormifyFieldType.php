<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_OPTIONS','FormifyOptions');
define('TABLE_FORMIFY_ANSWERS','FormifyAnswers');
define('TABLE_FORMIFY_RULES','FormifyRules');

use Core;
use Loader;
use Package;

class FormifyFieldType {
	
	public $name;
	public $iconClass;
	public $properties;
	private $config;
	
	public function get($handle) {
		$ft = new self;
		$ft->handle = $handle;
		$ft->loadConfig();
		$ft->hasOptions = $ft->hasOptions();
		$ft->properties = $ft->getProperties();
		return $ft;
	}
	
	public function all() {
		$pkg = Package::getByHandle('formify');
		$pkgPath = $pkg->getPackagePath();
		$ftPath = $pkgPath . '/elements/field_types';
		
		$fh = Loader::helper('file');
		$ftFiles = $fh->getDirectoryContents($ftPath);
		sort($ftFiles);
		
		$fieldTypes = array();
		
		if(count($ftFiles) > 0) {
			foreach($ftFiles as $ftHandle) {
				$ft = self::get($ftHandle);
				if($ft) {
					$fieldTypes[] = $ft;
				}				
			}
		}
		
		return $fieldTypes;
	}
	
	public function loadConfig() {
		$pkg = Package::getByHandle('formify');
		$pkgPath = $pkg->getPackagePath();
		$configFile = $pkgPath . '/elements/field_types/' . $this->handle . '/config.xml';
		$configXML = simplexml_load_file($configFile);

		$this->config = $configXML;
		$this->name = (string) $configXML['name'];
		$this->iconClass = (string) $configXML['iconclass'];

		return $configXML;
	}
	
	public function hasOptions() {
		if($this->config['hasoptions'] == 'true') {
			return true;
		} else {
			return false;
		}
	}
	
	public function hasInput() {
		if($this->config['hasinput'] == 'false') {
			return false;
		} else {
			return true;
		}
	}
	
	public function getHeaderItems() {
		$headerItems = array();
		if(count($this->config->headeritems) > 0) {
			foreach($this->config->headeritems->item as $hiXML) {
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
		if(count($this->config->footeritems) > 0) {
			foreach($this->config->footeritems->item as $fiXML) {
				$footerItem = array();
				$footerItem['type'] = (string) $fiXML['type'];
				$footerItem['file'] = (string) $fiXML['file'];
				$footerItem['package'] = (string) $fiXML['package'];
				$footerItems[] = $footerItem;
			}
		}
		return $footerItems;
	}
	
	public function getProperties() {
		$properties = array();
		if(count($this->config->properties->property) > 0) {
			foreach($this->config->properties->property as $p) {
				$properties[] = (string) $p['handle'];
			}
		}
		return $properties;
	}
	
	public function hasProperty($property) {
		foreach($this->getProperties() as $p) {
			if($p == $property) {
				return true;
			}
		}
		return false;
	}
	
	public function render($ff) {
		Loader::packageElement('field_types/' . $this->handle . '/view','formify',array('ft'=>$this,'field'=>$ff));
	}
	
	public function renderSelected($value,$comparison) {
		if($value == $comparison) {
			return 'selected="selected"';
		}
	}
		
}