<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace minty\seeds\controller;

define("UPLOAD_BASE", 'minty/uploads/seeds/');
define("UPLOAD_TEMP", 'minty/uploads/temp/');
define("TABLE_UPLOAD", 'minty_sl_uploads');

class upload_controller {

	protected $auth;
	protected $user;
	protected $request;
	protected $config;
	protected $helper;
	protected $template;
	protected $language;
	protected $db;
	protected $file_factory;
	protected $log;
	protected $php_ext;
	protected $phpbb_root_path;
	protected $points_manager; 

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\user $user,
		\phpbb\request\request $request, 
		\phpbb\config\config $config, 
		\phpbb\controller\helper $helper, 
		\phpbb\template\template $template, 
		\phpbb\language\language $language,
		\phpbb\db\driver\factory $dbal,
		\phpbb\files\factory $file_factory,
		\phpbb\log\log $log,
		$phpbb_root_path, 
		$phpEx,
		\phpbbstudio\aps\actions\manager $points_manager = null
		) {
			$this->auth = $auth;					
			$this->user = $user;					
			$this->request = $request;	
			$this->config	= $config;
			$this->helper	= $helper;
			$this->template	= $template;
			$this->language	= $language;
			$this->db = $dbal;
			$this->file_factory = $file_factory;		
			$this->log	= $log;
			$this->php_ext = $phpEx;
			$this->phpbb_root_path = $phpbb_root_path;
			$this->points_manager = $points_manager;	
	}

	public function handle($mode) {
		require_once("./config." . $this->php_ext); 
		$this->request->enable_super_globals();
		$json = null;
		if ($mode == 'breeder_upload') {
			$json = $this->processFileUpload();		
		} 
		$json_response = new \phpbb\json_response();
		$json_response->send($json);
	}

	function processFileUpload() {
		$upload = $this->file_factory->get('files.upload');
		$upload->set_allowed_extensions($this->getAllowedExtensions());
		$filespec = $upload->handle_upload('files.types.form', 'upload');
		$filespec->clean_filename();
		$upload->common_checks($filespec);
		$filespec->move_file($this->getTempDir());
		$this->recordFileUpload($filespec);
	}
	
	function recordFileUpload($filespec) {
		$id = $this->request->variable('upload_id','');
		if ($this->canAddBreeder()) {
			$sql_ary = array(
				'upload_id'	=> $this->db->sql_escape($id),
				'name'	=> $filespec->get('realname'),
				'path'	=> '/app.php/downloads/minty/todo',
				'size'	=> $filespec->get('filesize'),
				'type'	=> $filespec->get('mimetype'),
				'user_id' => $this->user->data['user_id'],
				'filename'=> $filespec->get('filename'),
				'realname'=> $filespec->get('realname'),
				'uploadname'=> $filespec->get('uploadname'),
				'extension'	=> $filespec->get('extension'),
			);

			$sql = ' INSERT INTO ' . $this->getDbPrefix().TABLE_UPLOAD . $this->db->sql_build_array('INSERT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$this->triggerAdvancedPointsSystemAction('UPLOAD_IMAGE', $id);
			$json = (object) [
				'id' => $id,
				'link' =>  'minty/uploads/temp/' . $filespec->get('realname'),
				'name' => $filespec->get('realname'),
				'size' => $filespec->get('filesize'),
				'status' => 'inprogress', // , "inprogress", "uploaded", or "failed")
				'path'	=> $filespec->get('filename'),
				//preview - (string) the path to the file preview. Can be set for a file with status:"uploaded". If the parameter isn't specified, Vault will generate preview automatically
			];
			$this->db->sql_freeresult($result);
			return $json;
		}
	}

	function getDbPrefix() {
		return $this->config['minty_seeds_db_prefix'];
	}

	function getBaseDir() {
		$dir = $this->phpbb_root_path . UPLOAD_BASE;
		mkdir($dir, 0777, true);
		return $dir;		
	}
	function getTempDir() {
		$tmp = $this->phpbb_root_path . UPLOAD_TEMP;
		mkdir($tmp, 0777, true);
		return $tmp;		
	}

	function getAllowedExtensions() {
		return array('jpg', 'jpeg', 'gif', 'png', 'webp');		
	}

	function triggerAdvancedPointsSystemAction($action, $data) {
		if ($this->isAdvancedPointsSystemIntegrationEnabled()) {
			$forum_ids = null;
			$user_ids = null;
			$this->points_manager->trigger($action, $user_ids, $data, $forum_ids);
		}
	}

	function isAdvancedPointsSystemIntegrationEnabled() {
		$aps_enabled = (bool) $this->config['minty_seeds_aps_enabled'];
		return $aps_enabled && $this->points_manager !== null;
	}

	function isEnabled() {
		return (bool)$this->config['minty_seeds_enabled'];
	}
	function isDebugging() {
		return (bool)$this->config['minty_seeds_debug'];
	}

	function isAdmin() {
		return (bool)($this->auth->acl_get('a_minty_seeds_admin'));
	}

	function canAddBreeder() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('m_minty_seeds_add_breeder'));
	}


}
