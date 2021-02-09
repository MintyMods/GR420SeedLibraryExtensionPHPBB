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

define("TABLE_PREFIX", "phpbb_");
define("TABLE_SEEDS", TABLE_PREFIX . "minty_sl_seeds");
define("TABLE_BREEDER", TABLE_PREFIX . "minty_sl_breeder");
define("TABLE_GENETICS", TABLE_PREFIX . "minty_sl_genetics");
define("TABLE_SMELLS", TABLE_PREFIX . "minty_sl_smell");
define("TABLE_EFFECTS", TABLE_PREFIX . "minty_sl_effect");
define("TABLE_TASTES", TABLE_PREFIX . "minty_sl_taste");
define("TABLE_META_TAGS", TABLE_PREFIX . "minty_sl_meta_tag");
define("TABLE_AWARDS", TABLE_PREFIX . "minty_sl_award");

class main_controller {
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

	public function __construct(\phpbb\request\request $request, 
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

	public function handle($name) {
		require_once("./config" . $this->php_ext); 

		$this->request->enable_super_globals();
		$json = null;
		if ($name == 'SEED_POST') {
			return $this->processSeedFormPost();		
		} else if ($name == 'BREEDER_UPLOAD') {
			$json = $this->processFileUpload();		
		} else if ($name == 'BREEDER_POST') {
			$json = $this->processBreederFormPost();		
		} else if ($name == 'breeder_id') {
			$json = $this->getBreederOptionsJson();	
		} else if ($name == 'BREEDER_SELECT_RECORD') {
			$json = $this->getBreedersRecordJson();	
		} else if ($name == 'GRID_SELECT_RECORDS') {
			$json = $this->getGridSelectJson();		
		} else if ($name == 'minty_sl_genetics') {
			$json = $this->getGeneticOptionsJson();	
		} else if ($name == 'minty_sl_smells') {
			$json = $this->getSmellsOptionsJson();	
		} else if ($name == 'minty_sl_effects') {
			$json = $this->geEffectsOptionsJson();	
		} else if ($name == 'minty_sl_tastes') {
			$json = $this->getTastesOptionsJson();	
		} else if ($name == 'minty_sl_meta_tags') {
			$json = $this->getMetaTagsOptionsJson();	
		} else if ($name == 'minty_sl_awards') {
			$json = $this->getAwardsOptionsJson();	
		} else  {
			$this->template->assign_var('SEEDS_MESSAGE', $this->language->lang($l_message, $name));
			return $this->helper->render('@minty_seeds/seeds_body.html', $name);
		} 
		$json_response = new \phpbb\json_response();
		$json_response->send($json);
	}

	function getBreedersRecordJson() {
		$result_list = array();
		$sql = 'SELECT * FROM ' . TABLE_BREEDER;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'					=> $row['breeder_id'],
				'breeder_name'			=> $row['breeder_name'],
				'breeder_desc' 			=> $row['breeder_desc'],
				'breeder_url' 			=> $row['breeder_url'],
				'sponsor_yn' 			=> $row['sponsor_yn'],
			);
		}
		$this->db->sql_freeresult($result);
		return (object) ['data' => $result_list];
	}

	function processSeedFormPost() {
		$seed_id = request_var('seed_id', 0);
		$seed_name = request_var('seed_name', '');
		// var_dump($this->request); 
	}

	function getSmellsOptionsJson() {
		$result_list = array();
		$sql = 'SELECT * FROM ' . TABLE_SMELLS;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'	=> $row['smell_id'],
				'value'	=> $row['smell_name'],
			);
		}
		$this->db->sql_freeresult($result);
		return $result_list;
	}

	function geEffectsOptionsJson() {
		$result_list = array();
		$sql = 'SELECT * FROM ' . TABLE_EFFECTS;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'	=> $row['effect_id'],
				'value'	=> $row['effect_name'],
			);
		}
		$this->db->sql_freeresult($result);
		return $result_list;
	}

	function getTastesOptionsJson() {
		$result_list = array();
		$sql = 'SELECT * FROM ' . TABLE_TASTES;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'	=> $row['taste_id'],
				'value'	=> $row['taste_name'],
			);
		}
		$this->db->sql_freeresult($result);
		return $result_list;
	}

	function getMetaTagsOptionsJson() {
		$result_list = array();
		$sql = 'SELECT * FROM ' . TABLE_META_TAGS;	
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'	=> $row['meta_tag_id'],
				'value'	=> $row['meta_tag_name'],
			);
		}
		$this->db->sql_freeresult($result);
		return $result_list;
	}

	function getAwardsOptionsJson() {
		$result_list = array();
		$sql = 'SELECT * FROM ' . TABLE_AWARDS;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'	=> $row['award_id'],
				'value'	=> $row['award_name'],
			);
		}
		$this->db->sql_freeresult($result);
		return $result_list;
	}

	function getBreederOptionsJson() {
		$result_list = array();
		$sql = 'SELECT breeder_id AS id, breeder_name AS value FROM ' . TABLE_BREEDER;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'	=> $row['id'],
				'value'	=> $row['value'],
			);
		}
		$this->db->sql_freeresult($result);
		return $result_list;
	}

	function getGeneticOptionsJson() {
		$result_list = array();
		$sql = 'SELECT S.seed_id AS id, CONCAT(S.seed_name, " - ", B.breeder_name)  AS value' .
			   ' FROM ' . TABLE_SEEDS . ' S, ' . TABLE_BREEDER . ' B' .
			   ' WHERE S.breeder_id = B.breeder_id';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'	=> $row['id'],
				'value'	=> $row['value'],
			);
		}
		$this->db->sql_freeresult($result);
		return $result_list;	
	}

	function getGridSelectJson() {
		$from = request_var('from', 0);
		$limit = request_var('limit', 0);
		$result_list = array();
		$sql = ' SELECT S.seed_id, S.seed_name, B.breeder_name,' . 
				    'S.flowering_type, S.sex, S.indoor_yn, S.outdoor_yn,' . 
					'S.flowering_time,	S.harvest_month, S.thc, S.cbd, ' .
					'S.indica, S.sativa, S.ruderalis, S.yeild_indoors, S.yeild_outdoors,' .
					'S.height_indoors, S.height_outdoors, S.vote_likes,' .
					'S.vote_dislikes, S.seed_desc, S.forum_url' . 
				' FROM ' . TABLE_SEEDS . ' S, ' . TABLE_BREEDER . ' B' .
				' WHERE S.breeder_id = B.breeder_id'  . 
				' AND S.seed_id >= ' . $this->db->sql_escape($from) . 
				' AND S.seed_id <= ' . $this->db->sql_escape(($from + $limit));

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$result_list[] = array(
				'id'					=> $row['seed_id'],
				'seed_name'				=> $row['seed_name'],
				'breeder_name'			=> $row['breeder_name'],
				'flowering_type' 		=> $this->convertType($row['flowering_type']),
				'sex' 					=> $this->convertSex($row['sex']),
				'indoor_yn' 			=> boolval($row['indoor_yn']),
				'outdoor_yn' 			=> boolval($row['outdoor_yn']),
				'flowering_time'	 	=> $row['flowering_time'],
				'harvest_month'			=> $row['harvest_month'],
				'thc'			 		=> $row['thc'],
				'cbd'			 		=> $row['cbd'],
				'indica'			 	=> $row['indica'],
				'sativa'			 	=> $row['sativa'],
				'ruderalis'			 	=> $row['ruderalis'],
				'yeild_indoors'		 	=> $row['yeild_indoors'],
				'yeild_outdoors'	 	=> $row['yeild_outdoors'],
				'height_indoors'	 	=> $row['height_indoors'],
				'height_outdoors'	 	=> $row['height_outdoors'],
				'vote_likes' 			=> $row['vote_likes'],
				'vote_dislikes' 		=> $row['vote_dislikes'],
				'seed_desc' 			=> $row['seed_desc'],
				'forum_url' 			=> $row['forum_url']
			);
		}
		$this->db->sql_freeresult($result);

		$json = (object) [
			'data' => $result_list,
			'total_count' => $this->getTotalRecordCount(),
			'from' => $from
		];
		return $json;			
	}

	function processFileUpload() {
		// $filespec = $this->file_factory->get('files.filespec');
		// $upload = $this->file_factory->get('files.upload')->set_allowed_extensions(array('jpg', 'jpeg', 'gif', 'png'));
		// $upload_dir = $this->phpbb_root_path . 'minty_uploads';
		// $files = $upload->handle_upload('files.types.form', 'file_upload');
		$upload_file = $this->request->file('breeder_logo');
		if (!empty($upload_file['breeder_logo'])) {
			$file = $upload->handle_upload('files.types.form', 'breeder_logo');
		}

	}

	function processBreederFormPost() {
		$name = request_var('breeder_name', '');
		$desc = request_var('breeder_desc', '');
		$url = request_var('breeder_url', '');
		$logo = request_var('breeder_logo', array());
		$sponsor = request_var('sponsor_yn', 'false') == 'true';
		$sql_ary = array(
			'breeder_name'	=> $this->db->sql_escape($name),
			'breeder_desc'	=> $this->db->sql_escape($desc),
			'breeder_url'	=> $this->db->sql_escape($url),
			'sponsor_yn'	=> $sponsor,
		);
		$sql = 'INSERT INTO ' . TABLE_BREEDER . $this->db->sql_build_array('INSERT', $sql_ary);
		$result = $this->db->sql_query($sql);
		$json = (object) [
			'saved' => $result,
			'id' => $this->getBreederId($name),
			'data' => $sql_ary
		];
		$this->db->sql_freeresult($result);
		return $json;
	}


	function getBreederId($name) {
		$sql = 'SELECT breeder_id AS id FROM ' . TABLE_BREEDER . 
		' WHERE breeder_name ="' . $this->db->sql_escape($name) . '"';
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$id = $row['id'];
		$this->db->sql_freeresult($result);
		return $id;	
	}

	function getTotalRecordCount() {
		$sql = 'SELECT count(*) AS count FROM ' . TABLE_SEEDS;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$count = $row['count'];
		$this->db->sql_freeresult($result);
		return $count;
	}

	function convertSex($sex) {
		if ($sex == 'M') {
			return 'Male';
		} else if ($sex == 'F') {
			return 'Female';
		} else {
			return '';
		}
	}

	function convertType($sex) {
		if ($sex == 'R') {
			return 'Regular';
		} else if ($sex == 'F') {
			return 'Feminised';
		} else if ($sex == 'A') {
			return 'Auto';
		} else {
			return '';
		}
	}
}
