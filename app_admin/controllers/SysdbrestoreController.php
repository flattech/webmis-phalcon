<?php
class SysDBRestoreController extends ControllerBase{
	public function initialize(){
		parent::initialize();
		// File
		$this->FileRoot = $_SERVER['DOCUMENT_ROOT'].APP_BACKUP;
	}
	// Index
	public function indexAction(){
		$File = new File();
		$File->file_root = $this->FileRoot;
		$this->view->setVar('Files',$File->lists(''));
		// Menus
		$this->view->setVar('Menus',$this->inc->getMenus());
		$this->tag->prependTitle($this->inc->Ctitle);
		// Data
		$this->view->setVar('Lang',$this->inc->getLang('system/sys_db'));
		$this->view->setVar('LoadJS', array('system/sys_db_restore.js'));
		// View
		$this->view->setTemplateAfter(APP_THEMES.'/main');
		$this->view->pick("system/db/restore/index");
	}
	/* Download */
	public function downAction(){
		$file = $this->FileRoot.$this->request->getQuery('file');
		if(is_file($file)){
			$File = new File();
			$File->down($file);
		}else{return FALSE;}
	}
	/* Import */
	public function impAction(){
		$this->view->setVar('File',$this->request->getPost('file'));
		$this->view->setVar('Lang',$this->inc->getLang('system/sys_db'));
		$this->view->pick("system/db/restore/imp");
	}
	/* Del */
	public function delAction(){
		$this->view->pick("system/db/restore/del");
	}
	/* Data */
	public function DataAction($type=''){
		if($this->request->isPost()){
			// Export
			if($type=='import'){
				$File = $this->FileRoot.trim($this->request->getPost('file'));
				if(!is_file($File)){return $this->Result('err');}
				/* Remove Notes */
				$content = file_get_contents($File);
				$content = preg_replace("/\n#\n# TABLE(.*)\s#\n\n/i","",$content);
				$sqls = array_filter(explode(";\n",$content));
				foreach($sqls as $sql){
					$sql = trim($sql);
					if(!empty($sql)){
						if($this->db->execute($sql)==FALSE){$this->Result('err');}
					}
				}
				return $this->Result('suc');
			}elseif($type=='delete'){
				$Files = json_decode($this->request->getPost('file'));
				foreach ($Files as $val){
					if(@unlink($this->FileRoot.$val)==FALSE){$this->Result('err');}
				}
				return $this->Result('suc');
			}
		}
	}
	private function Result($type=''){
		$lang = $this->inc->getLang('msg');
		if($type=='suc'){
			return $this->response->setJsonContent(array('status'=>'y'));
		}elseif($type=='err'){
			return $this->response->setJsonContent(array('status'=>'n','title'=>"'.$lang->_('msg_title').'",'msg'=>"'.$lang->_('msg_err').'",'text'=>"'.$lang->_('msg_auto_close').'"));
		}
	}
}