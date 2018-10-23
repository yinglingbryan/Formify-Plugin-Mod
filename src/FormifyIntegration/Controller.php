<?php   
namespace Concrete\Package\Formify\Src\FormifyIntegration;

abstract class Controller {
	
	public function validate($f) {
		return true;
	}
	
	public function process($r,$mode='add') {
		$this->r = $r;
		$this->f = $r->getForm();
		
		if($mode == 'add') {
			return static::add($r);
		} else {
			return static::edit($r);
		}
		
	}
	
	public function add($r) {
		return true;
	}
	
	public function edit($r) {
		return true;
	}
	
	public function finalize($r) { // Used to override AJAX response
	}
	
	public function getRecord() {
		return $this->r;
	}
	
	public function getForm() {
		return $this->f;
	}
	
	
}