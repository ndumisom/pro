<?php

class SowsController extends AppController {

	public $helpers = array('Html', 'Form');

	/* function to display all files details */

	public function index() {
		$this->set('Reports', $this->Sow->find('all'));
	}

	/* function to add file record into the database */

	public function add($id=NULL) {
		if ($this->request->is('post')) {
			$this->Sow->create();             
			if (empty($this->data['Sow']['report']['name'])) {
				unset($this->request->data['Sow']['report']);
			}
			if (!empty($this->data['Sow']['report']['name'])) {
				$file = $this->data['Sow']['report'];
				$file['name'] = $this->sanitize($file['name']);
				$this->request->data['Sow']['report'] = time() . $file['name'];
				$this->request->data['Sow']['order_id']=$id;

				if ($this->Sow->saveAll($this->request->data)) {
					move_uploaded_file($file['tmp_name'], APP . 'outsidefiles' . DS . time() . $file['name']);
					$this->Session->setFlash(__('Your Report has been saved.'));
					return $this->redirect(array('action' => 'index'));
				}
			}
			$this->Session->setFlash(__('Unable to add your Report.'));
		}
	}

	function sanitize($string, $force_lowercase = true, $anal = false) {
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ">", "/", "?");
		$clean = trim(str_replace($strip, "", strip_tags($string)));
		$clean = preg_replace('/\s+/', "-", $clean);
		$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean;
		return ($force_lowercase) ?
				(function_exists('mb_strtolower')) ?
						mb_strtolower($clean, 'UTF-8') :
						strtolower($clean) :
				$clean;
	}

	public function view($id = null) {
		if (!$id) {
			throw new NotFoundException(__('Invalid Report'));
		}

		$Report = $this->Sow->findById($id);
		if (!$Report) {
			throw new NotFoundException(__('Invalid Report'));
		}
		$this->set('Report', $Report);
	}
	
	public function viewdown($id=null,$download=false) {
		
	    
     if($download){
      $download=true;
     }

     $files=$this->Sow->findById($id);
      $filename=$files['Sow']['report'];
      $name=explode('.',$filename);
     $this->viewClass = 'Media';
     
      // path will be app/outsidefiles/yourfilename.pdf
      $params = array(
            'id'        => $filename,
            'name'      => $name[0],
             'download'  => $download,
            'extension' => 'docx',
            'path'      => 'outsidefiles' . DS
        );
        
     $this->set($params);
    }


}

?>