<?php   
namespace Concrete\Package\Formify\Src;

define('TABLE_FORMIFY_FORMS','FormifyForms');
define('TABLE_FORMIFY_PERMISSIONS','FormifyPermissions');
define('TABLE_FORMIFY_FIELDS','FormifyFields');
define('TABLE_FORMIFY_NOTIFICATIONS','FormifyNotifications');
define('TABLE_FORMIFY_RECORDS','FormifyRecords');

use \Concrete\Package\Formify\Src\FormifyObject;	
use \Concrete\Package\Formify\Src\FormifyField;	
use \Concrete\Package\Formify\Src\FormifyNotification;	
use Loader;
use Log;
use User;
use Package;

class FormifyFormSection {

	public function addField($ff) {
		$this->fields[] = $ff;
	}
	
	public function getFields() {
		return $this->fields;
	}
	
}