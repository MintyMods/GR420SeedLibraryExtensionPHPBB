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

class upload_controller {
	
	const ALLOWED_EXTENSIONS = array('jpg', 'jpeg', 'gif', 'png', 'webp', 'svg');

	const UPLOAD_TEMP = "minty/temp";
	const TABLE_UPLOAD = "minty_sl_upload";
	const TABLE_UPLOADS = "minty_sl_uploads";

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
		$phpEx
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
	}

	public function handle($mode) {
		require_once("./config." . $this->php_ext); 
		$this->request->enable_super_globals();
		$json[] = null;
		if ($mode == 'breeder_upload') {
			if ($this->canAddBreeder()) {
				$json = $this->processFileUpload();		
			}
		} else if ($mode == 'seed_upload') {
			if ($this->canAdd()) {
				$json = $this->processFileUpload();		
			}
		} else if ($mode == 'list_files') {
			$json = $this->getFileList();
		}

		$json_response = new \phpbb\json_response();
		$json_response->send($json);
	}
	
	function getFileList() {
		$json = [];
		$seed_id = $this->request->variable('seed_id',0);
		$breeder_id = $this->request->variable('breeder_id',0);
		$sql =  ' SELECT * FROM ' . $this->getUploadsTable() . 
				' WHERE breeder_id = ' . $this->db->sql_escape($breeder_id) .
				' AND seed_id = ' . $this->db->sql_escape($seed_id) . 
				' AND breeder_id != 0';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$json[] = array(
				'name' => $row['uploadname'],
				'title' => $row['uploadname'],
				'src' => '../..' . $row['path'],
				'path' => $row['path'],
				'status' => "uploaded",
				'id'	=> $row['id'],
				'file' => array(
					'name'	=> $row['uploadname'],
					'type'	=> $row['type'],
					'size'	=> (int)$row['size'],
				)
			);
		}
		$this->db->sql_freeresult($result);
		return $json;
	}

	function processFileUpload() {
		$upload = $this->file_factory->get('files.upload');
		$upload->set_allowed_extensions($this->getAllowedExtensions());
		$filespec = $upload->handle_upload('files.types.form', 'upload');
		$filespec->clean_filename('unique_ext');
		$upload->common_checks($filespec);
		if ($filespec->error) {
			return $upload->error;
		}
		if ($this->moveTempFile($filespec)) {
			return $this->recordFileUpload($filespec);
		}
	}
	
	function moveTempFile($filespec) {
		$overwrite = true;
		$skip_image_check = false;
		$filespec->move_file($this->getTempDir(), $overwrite, $skip_image_check);
		return (bool) $filespec->get('file_moved');	
	}

	function recordFileUpload($filespec) {
		$id = $this->request->variable('upload_id','');
		$sql_ary = array(
			'upload_id'	=> $this->db->sql_escape($id),
			'name'	=> $filespec->get('name'),
			'size'	=> $filespec->get('filesize'),
			'status' => 'uploaded',
			'type'	=> $filespec->get('mimetype'),
			'user_id' => $this->user->data['user_id'],
			'filename'=> $filespec->get('destination_file'),
			'realname'=> $filespec->get('realname'),
			'uploadname'=> $filespec->get('uploadname'),
			'extension'	=> $filespec->get('extension'),
		);

		$sql = ' INSERT INTO ' . $this->getUploadsTable() . $this->db->sql_build_array('INSERT', $sql_ary);
		$result = $this->db->sql_query($sql);
		$json = (object) [
			'id' => $id,
			'link' =>  '/minty/uploads/' . $filespec->get('realname'),
			'name' => $filespec->get('realname'),
			'size' => $filespec->get('filesize'),
			'status' => 'uploaded', 
			'path'	=> $filespec->get('filename'),
		];
		$this->db->sql_freeresult($result);
		return $json;
	}

	function getUploadTable() {
		return $this->getDbPrefix() . self::TABLE_UPLOAD;
	}

	function getUploadsTable() {
		return $this->getDbPrefix() . self::TABLE_UPLOADS;
	}

	function getDbPrefix() {
		return $this->config['minty_seeds_db_prefix'];
	}

	function getDownloadDirectory() {
		return '/' . self::UPLOAD_TEMP + '/';
	}

	function getTempDir() {
		$tmp = $this->phpbb_root_path . self::UPLOAD_TEMP;
		if (!file_exists ($tmp)) {
			mkdir($tmp, 0777, true);
		}
		return $tmp;		
	}

	function getAllowedExtensions() {
		return self::ALLOWED_EXTENSIONS;		
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

	function canAdd() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('u_minty_seeds_add'));
	}

	function canAddBreeder() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('m_minty_seeds_add_breeder'));
	}


}
