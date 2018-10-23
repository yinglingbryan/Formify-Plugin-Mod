<?php  
namespace Concrete\Package\Formify\Controller\Api;

use \Concrete\Package\Formify\Src\FormifyForm;
use Concrete\Core\Application\Service\Dashboard;
use Controller;
use FileImporter;
use Loader;
use Log;
	
class File extends Controller {
	
	public function upload() {
		
		$uh = Loader::helper('concrete/urls');
		
		//Upload file
		Loader::library("file/importer");
		$fi = new FileImporter();
		$resp = $fi->import($_FILES['file']['tmp_name'], $_FILES['file']['name']);
		if (!($resp instanceof \Concrete\Core\File\Version)) {
			switch($resp) {
				case FileImporter::E_FILE_INVALID_EXTENSION:
					$errors['fileupload'] = t('Invalid file extension.');
					break;
				case FileImporter::E_FILE_INVALID:
					$errors['fileupload'] = t('Invalid file.');
					break;
			}
		} else {
			$response = array();
			$response['status'] = 'success';
			$response['fileID'] = $resp->getFileID();
			$js = Loader::helper('json');
			$r = $js->encode($response);
			echo $r;
		}
			
	}
	
}