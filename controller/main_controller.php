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

class main_controller {

	const ALLOWED_EXTENSIONS = array('jpg', 'jpeg', 'gif', 'png', 'webp');

	const UPLOAD_BASE = "minty/seeds";
	const UPLOAD_TEMP = "minty/temp";

	const TABLE_SEEDS = "minty_sl_seeds";
	const TABLE_BREEDER = "minty_sl_breeder";
	const TABLE_GENETICS = "minty_sl_genetics";
	const TABLE_PARENT = "minty_sl_parent";
	const TABLE_PARENTS = "minty_sl_parents";
	const TABLE_SMELLS = "minty_sl_smells";
	const TABLE_EFFECTS = "minty_sl_effects";
	const TABLE_TASTES = "minty_sl_tastes";
	const TABLE_META_TAGS = "minty_sl_meta_tags";
	const TABLE_AWARDS = "minty_sl_awards";
	const TABLE_SEED = "minty_sl_seed";
	const TABLE_SMELL = "minty_sl_smell";
	const TABLE_EFFECT = "minty_sl_effect";
	const TABLE_TASTE = "minty_sl_taste";
	const TABLE_META_TAG = "minty_sl_meta_tag";
	const TABLE_AWARD = "minty_sl_award";
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

	public function handle($name) {
		require_once("./config." . $this->php_ext); 
		$this->request->enable_super_globals();
		$json = null;
		if ($name == 'GRID_SELECT_RECORDS') {
			$json = $this->getGridSelect();	
		} else if ($name == 'minty_sl_seeds') {
			$json = $this->processSeedFormPost();		
		} else if ($name == 'minty_sl_breeder') {
			$json = $this->processBreederFormPost();		
		} else if ($name == 'breeder_id') {
			$json = $this->getBreederOptions();	
		} else if ($name == 'BREEDER_SELECT_RECORD') {
			$json = $this->getBreedersRecord();	
		} else if ($name == 'GRID_DELETE_RECORD') {
			$json = $this->processGridDelete();	
		} else if ($name == 'minty_sl_genetics') {
			$json = $this->getGeneticOptions();	
		} else if ($name == 'minty_sl_parents') {
			$json = $this->getParentsOptions();	
		} else if ($name == 'minty_sl_smells') {
			$json = $this->getSmellsOptions();	
		} else if ($name == 'minty_sl_effects') {
			$json = $this->geEffectsOptions();	
		} else if ($name == 'minty_sl_tastes') {
			$json = $this->getTastesOptions();	
		} else if ($name == 'minty_sl_meta_tags') {
			$json = $this->getMetaTagsOptions();	
		} else if ($name == 'minty_sl_awards') {
			$json = $this->getAwardsOptions();	
		} else  {
			$this->template->assign_vars(array(
				'SEEDS_MESSAGE' => $this->language->lang($l_message, $name),
				'MINTY_SEEDS_ENABLED' => $this-> isEnabled(),
				'MINTY_SEEDS_DEBUG' => $this->isDebugging(),
				'USER_MINTY_SEEDS_ENABLED' => $this->isUserEnabled(),
				'USER_MINTY_SEEDS_SPLIT_ENABLED' => $this->isUserSplitEnabled(),
				'A_MINTY_SEEDS_ADMIN'	=> $this->isAdmin(),
				'M_MINTY_SEEDS_ADD_BREEDER'	=> $this->canAddBreeder(),
				'M_MINTY_SEEDS_EDIT_BREEDER'	=> $this->canEditBreeder(),
				'M_MINTY_SEEDS_DELETE_BREEDER'	=> $this->canDeleteBreeder(),
				'U_MINTY_SEEDS_ADD'	=> $this->canAdd(),
				'U_MINTY_SEEDS_EDIT' => $this-> canEdit(),
				'U_MINTY_SEEDS_DELETE' => $this->canDelete(),
				'U_MINTY_SEEDS_READ' => $this->canRead(),
			));
			return $this->helper->render('@minty_seeds/seeds_body.html', $name);
		} 
		$json_response = new \phpbb\json_response();
		if ($json == null) {
			$json_response->send([]);
		} else {
			$json_response->send($json);
		}
	}

	function triggerAdvancedPointsSystemAction($action, $data) {
		if ($this->isAdvancedPointsSystemIntegrationEnabled()) {
			$forum_ids = null;
			$user_ids = null;
			$this->points_manager->trigger($action, $user_ids, $data, $forum_ids);
		}
	}

	function getDbPrefix() {
		return $this->config['minty_seeds_db_prefix'];
	}

	function isAdvancedPointsSystemIntegrationEnabled() {
		$aps_enabled = (bool) $this->config['minty_seeds_aps_enabled'];
		return $aps_enabled && $this->points_manager !== null;
	}

	function processSeedFormPost() {
		$seed_id = $this->request->variable('seed_id', 0);
		if ($this->seedRecordExists($seed_id)) {
			$this->triggerAdvancedPointsSystemAction('UPDATE_SEED_RECORD', $seed_id);
			$result =  $this->updateSeedRecord($seed_id);
		} else {
			$this->triggerAdvancedPointsSystemAction('INSERT_SEED_RECORD', $seed_id);
			$result =  $this->insertNewSeedRecord();
			$seed_id = $result["seed_id"];
		}
		$this->processComboPostedOptions($seed_id);	
		$this->processUploads($result['breeder_id'], $result['seed_id']);
		return $result;
	}
	
	function processUploads($breeder_id, $seed_id) {
		$uploads = explode(",", $this->request->variable('upload_id', ''));
		$user_id = $this->getUserId();
		foreach ($uploads as $id) {
			if (substr($id, 0, 1) == 'u') {
				$file_info = $this->getFileInfoFromId($id);
				$upload = $this->file_factory->get('files.upload');
				$upload->set_allowed_extensions($this->getAllowedExtensions());				
				$file_name = $file_info['realname'];
				$filespec = $upload->handle_upload('files.types.local', $this->getTempDir() . $file_name, $file_info);
				$dest = $this->getBaseDir() . 'B' . $breeder_id . '/S' . $seed_id . '/U' . $user_id;
				$this->moveUploadedFile($filespec, $dest);
				$path = '/' . $dest . '/' . $filespec->get('realname');
			}
			$sql_ary = array(
				'seed_id'	 => $seed_id,
				'breeder_id' => $breeder_id,
				'user_id'	 => $user_id,
				'path'	 => $path,
			);		
			$sql = 'UPDATE ' . $this->getUploadsTable() . ' SET ' . 
			$this->db->sql_build_array('UPDATE', $sql_ary) .
			' WHERE upload_id ="' . $id . '"';
			$this->db->sql_query($sql);
		}
	}
	function moveUploadedFile($filespec, $dest) {
		if (!file_exists ($dest)) {
			mkdir($dest, 0777, true);
		}
		return $filespec->move_file($dest, true, false);
	}

	function getFileInfoFromId($id) {
		$sql = 'SELECT * FROM ' . $this->getUploadsTable() . ' WHERE upload_id = "' . $id . '"';
	 	$result = $this->db->sql_query($sql);	
		$file_info = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);	
		return $file_info;
	}

	function getTablePrefixFromComboName($name) {
		$prefix = str_replace('minty_sl_', '', $name); 
		$prefix = substr($prefix, 0, strlen($prefix) - 1);
		return $prefix;
	}

	function processComboPostedOptions($seed_id) {		
		if ($this->canEdit() || $this->canAdd()) {
			$this->processComboOptions('minty_sl_genetics', $seed_id);
			$this->processComboOptions('minty_sl_parents', $seed_id);
			$this->processComboOptions('minty_sl_awards', $seed_id);
			$this->processComboOptions('minty_sl_smells', $seed_id);
			$this->processComboOptions('minty_sl_tastes', $seed_id);
			$this->processComboOptions('minty_sl_effects', $seed_id);
			$this->processComboOptions('minty_sl_meta_tags', $seed_id);
		}
	}

	function deleteExistingComboRecords($name, $seed_id) {
		if ($this->canDelete()) {
			return $this->db->sql_query('DELETE FROM ' . $this->getDbPrefix().$name . ' WHERE seed_id = ' . $seed_id);
		}
	}

	function insertNewComboRecord($name, $seed_id, $value) {
		if ($this->canAdd()) {
			$prefix = $this->getTablePrefixFromComboName($name);
			$sql_ary = array(
				'seed_id'		=> $seed_id,
				$prefix . '_id'	=> $this->parseComboValue($name, $seed_id, $value, $prefix)
			);
			$sql = ' INSERT INTO ' . $this->getDbPrefix().$name . $this->db->sql_build_array('INSERT', $sql_ary);
			$this->db->sql_query($sql);
		}
	}
	
	function processComboOptions($name, $seed_id) {
		$values = $this->request->variable($name, array('' => ''), true);
		$this->deleteExistingComboRecords($name, $seed_id);
		foreach ($values as $value) {
			$this->insertNewComboRecord($name, $seed_id, $value);
		}
	}

	function parseComboValue($name, $seed_id, $value, $prefix) {
		if (strlen($value) > 1 && substr($value, 0, 4) === 'TAG[') {
			return intval($this->addNewUserTag($name, $seed_id, $value, $prefix));
		}
		return intval($value);	
	}

	function addNewUserTag($table, $seed_id, $tag, $prefix) {
		if ($this->canAdd()) {
			$parsed = substr($tag, 4, (strlen($tag) -5));
			$sql_ary = array(
				$prefix . '_name'	=> $parsed,
				$prefix . '_desc'	=> '** added dynamically by seed id ' . $seed_id . ' **',
				'user_id'	 => $this->getUserId()
			);
			$sql = ' INSERT INTO ' . $this->getDbPrefix() . 'minty_sl_'.$prefix . 
				$this->db->sql_build_array('INSERT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
			return $this->getComboTagId($table, $seed_id, $parsed, $prefix);
		}
	}

	function getComboTagId($table, $seed_id, $value, $prefix) {
		if ($this->canRead()) {
			$table = substr($table, 0, strlen($table)-1);
			$sql = ' SELECT ' . $prefix . '_id FROM ' . $this->getDbPrefix() . $table . 
					' WHERE ' . $prefix . '_name = \'' . $value . '\'';
			$result = $this->db->sql_query($sql);
			if ($row = $this->db->sql_fetchrow($result))	{
				$this->db->sql_freeresult($result);
				return $row[$prefix . '_id'];
			}
		}
		return -1;		
	}

	function getBreedersRecord() {
		if ($this->canRead()) {
			$sql = ' SELECT * FROM ' . $this->getBreederTable();
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
	}

	function insertNewSeedRecord() {
		if ($this->canAdd()) {
			$seed_name = $this->request->variable('seed_name', '');
			$breeder_id = $this->request->variable('breeder_id', 0);
			$sql_ary = $this->buildSqlArrayFromSeedFormRequest($seed_name, $breeder_id);
			$sql = ' INSERT INTO ' . $this->getSeedsTable() . $this->db->sql_build_array('INSERT', $sql_ary);
			if ($this->db->sql_query($sql)) {
				return $this->getSeedRecord($this->getSeedIdFromNameAndBreeder($seed_name, $breeder_id));
			}
		}
	}

	function getSeedRecord($seed_id) {
		if ($this->canRead()) {
			$sql = ' SELECT * FROM ' . $this->getSeedsTable() . ' WHERE seed_id = ' . $this->db->sql_escape($seed_id);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			return $row;
		}
	}

	function buildSqlArrayFromSeedFormRequest($seed_name, $breeder_id) {
		$sql_ary = array(
			'seed_name'			=> $seed_name,
			'breeder_id'		=> $breeder_id,
			'flowering_type'	=> $this->request->variable('flowering_type', ''),
			'sex'				=> $this->request->variable('sex', ''),
			'indoor_yn'			=> (int)($this->request->variable('indoor_yn','') == 'true'),
			'outdoor_yn'		=> (int)($this->request->variable('outdoor_yn', '') == 'true'),
			'thc'				=> $this->request->variable('thc', ''),
			'cbd'				=> $this->request->variable('cbd', ''),
			'indica'			=> $this->request->variable('indica', ''),
			'sativa'			=> $this->request->variable('sativa', ''),
			'ruderalis'			=> $this->request->variable('ruderalis', ''),
			'yeild_indoors'		=> $this->request->variable('yeild_indoors', ''),
			'yeild_outdoors'	=> $this->request->variable('yeild_outdoors', ''),
			'height_indoors'	=> $this->request->variable('height_indoors', ''),
			'height_outdoors'	=> $this->request->variable('height_outdoors', ''),
			'flowering_time'	=> $this->request->variable('flowering_time', ''),
			'harvest_month'		=> $this->request->variable('harvest_month', ''),
			'seed_desc'			=> $this->request->variable('seed_desc', ''),
			'forum_url'			=> $this->request->variable('forum_url', ''),
			'user_id'	 		=> $this->getUserId()
		);	
		return $sql_ary;	
	}

	function getSeedIdFromNameAndBreeder($name, $breeder) {
		if ($this->canRead()) {
			$sql = ' SELECT seed_id FROM ' . $this->getSeedsTable() . ' WHERE breeder_id = ' . 
				$this->db->sql_escape($breeder) . 
				' AND seed_name = "' . $this->db->sql_escape($name) . '"';
			$result = $this->db->sql_query($sql);
			if ($row = $this->db->sql_fetchrow($result)) {
				$this->db->sql_freeresult($result);
				return $row['seed_id'];
			}
		}
	}

	function getParentsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT parent_name as value, parent_id as id' . 
				   ' FROM ' . $this->getParentTable() . 
				   ' GROUP BY parent_name ORDER BY parent_name';
			$rs = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($rs);
			$this->db->sql_freeresult($rs);
		}
		return $rows;
	}

	function getSmellsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT smell_name as value, smell_id as id' . 
				   ' FROM ' . $this->getParentTable() . 
				   ' GROUP BY smell_name ORDER BY smell_name';
			$rs = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($rs);
			$this->db->sql_freeresult($rs);
		}
		return $rows;
	}

	function geEffectsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT effect_name as value, effect_id as id' . 
				   ' FROM ' . $this->getParentTable() . 
				   ' GROUP BY effect_name ORDER BY effect_name';
			$rs = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($rs);
			$this->db->sql_freeresult($rs);
		}
		return $rows;
	}

	function getTastesOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT taste_name as value, taste_id as id' . 
				   ' FROM ' . $this->getParentTable() . 
				   ' GROUP BY taste_name ORDER BY taste_name';
			$rs = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($rs);
			$this->db->sql_freeresult($rs);
		}
		return $rows;
	}

	function getMetaTagsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT meta_tag_name as value, meta_tag_id as id' . 
				   ' FROM ' . $this->getParentTable() . 
				   ' GROUP BY meta_tag_name ORDER BY meta_tag_name';
			$rs = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($rs);
			$this->db->sql_freeresult($rs);			
		}
		return $rows;
	}

	function getAwardsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT award_name as value, award_id as id' . 
				   ' FROM ' . $this->getParentTable() . 
				   ' GROUP BY award_name ORDER BY award_name';
			$rs = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($rs);
			$this->db->sql_freeresult($rs);	
		}
		return $rows;
	}

	function getBreederOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT breeder_id AS id, breeder_name AS value FROM ' . 
					$this->getBreederTable() . ' GROUP BY breeder_name ' .
					' ORDER BY breeder_name';
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$result_list[] = array(
					'id'	=> $row['id'],
					'value'	=> $row['value'],
				);
			}
			$this->db->sql_freeresult($result);
		}
		return $result_list;
	}

	function getGridSelect() {
		if ($this->canRead()) {
			$from = $this->request->variable('from', 0);
			$limit = $this->request->variable('limit', 0);
			$total_count = $this->getTotalRecordCount();
			$result_list = array();
			$sql = ' SELECT S.seed_id, S.seed_name, B.breeder_id, B.breeder_name,' . 
						'S.flowering_type, S.sex, S.indoor_yn, S.outdoor_yn,' . 
						'S.flowering_time,	S.harvest_month, S.thc, S.cbd, ' .
						'S.indica, S.sativa, S.ruderalis, S.yeild_indoors, S.yeild_outdoors,' .
						'S.height_indoors, S.height_outdoors, S.vote_likes,' .
						'S.vote_dislikes, S.seed_desc, S.forum_url' . 
					' FROM ' . $this->getSeedsTable() . ' S, ' . 
							   $this->getBreederTable() . ' B' .
					' WHERE S.breeder_id = B.breeder_id';

			$result = $this->db->sql_query_limit($sql, $limit, $from);
			while ($row = $this->db->sql_fetchrow($result))	{
				$seed_id = $row['seed_id'];
				$result_list[] = array(
					'id'					=> $seed_id,
					'seed_name'				=> $row['seed_name'],
					'breeder_id'			=> $row['breeder_id'],
					'breeder_name'			=> $row['breeder_name'],
					'flowering_type' 		=> $this->getUpperCaseCharOption($row['flowering_type']),
					'sex' 					=> $this->getUpperCaseCharOption($row['sex']),
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
					'forum_url' 			=> $row['forum_url'],
					self::TABLE_GENETICS			=> $this->getComboGeneticOptions($seed_id),
					self::TABLE_PARENTS			=> $this->getComboOptions(self::TABLE_PARENTS, $seed_id),
					self::TABLE_SMELLS			=> $this->getComboOptions(self::TABLE_SMELLS, $seed_id),
					self::TABLE_EFFECTS			=> $this->getComboOptions(self::TABLE_EFFECTS, $seed_id),
					self::TABLE_TASTES			=> $this->getComboOptions(self::TABLE_TASTES, $seed_id),
					self::TABLE_META_TAGS			=> $this->getComboOptions(self::TABLE_META_TAGS, $seed_id),
					self::TABLE_AWARDS			=> $this->getComboOptions(self::TABLE_AWARDS, $seed_id),
				);
			}
			$this->db->sql_freeresult($result);
		}
		$json = (object) [
			'data' => $result_list,
			'total_count' => $total_count,
			'from' => $from
		];
		return $json;			
	}

	function getComboOptions($table, $seed_id) {
		if ($this->canRead()) {
			$result_list = array();
			$name = $this->getTablePrefixFromComboName($table);
			$sql = ' SELECT ' . $name . '_id AS id FROM ' . $this->getDbPrefix().$table . ' WHERE seed_id = ' . $seed_id;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$result_list[] = $row['id'];
			}
			$this->db->sql_freeresult($result);
			return $result_list;	
		}
	}

	function getGeneticDescription($seed_id) {
		if ($this->canRead()) {
			$sql = ' SELECT ' . $seed_id . ' AS id, CONCAT(S.seed_name, " - ", B.breeder_name)  AS value' .
				' FROM ' . $this->getSeedsTable() . ' S, ' . $this->getBreederTable() . ' B' .
				' WHERE S.breeder_id = B.breeder_id' .
				' AND S.seed_id = ' . $seed_id;
			$result = $this->db->sql_query($sql);		
			if ($row = $this->db->sql_fetchrow($result)) {
				$this->db->sql_freeresult($result);
				return $row;
			}
		}
	}

	function getGeneticOptions() {
		if ($this->canRead()) {
			// @todo sort out paging for this option
			$result_list = array();
			$sql = ' SELECT S.seed_id AS id, CONCAT(S.seed_name, " - ", B.breeder_name)  AS value' .
				' FROM ' . $this->getSeedsTable() . ' S, ' . $this->getBreederTable() . ' B' .
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
	}

	function getComboGeneticOptions($seed_id) {
		if ($this->canRead()) {
			$descriptions = array();
			$sql = ' SELECT genetic_id FROM ' . $this->getGeneticsTable() . ' WHERE seed_id = ' . $seed_id;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$descriptions[] = $row['genetic_id'];
			}
			$this->db->sql_freeresult($result);
			return $descriptions;
		}
	}

	function processBreederFormPost() {
		// $this->triggerAdvancedPointsSystemAction('EDIT_BREEDER_RECORD', $breeder_id);
		// $this->triggerAdvancedPointsSystemAction('DELETE_BREEDER_RECORD', $breeder_id);
		if ($this->canAddBreeder()) {
			$breeder_id = $this->request->variable('breeder_id', 0);
			$name = $this->request->variable('breeder_name', '');
			$desc = $this->request->variable('breeder_desc', '');
			$url = $this->request->variable('breeder_url', '');
			$sponsor = $this->request->variable('sponsor_yn', 'false') == 'true';
			$sql_ary = array(
				'breeder_name'	=> $name,
				'breeder_desc'	=> $desc,
				'breeder_url'	=> $url,
				'sponsor_yn'	=> $sponsor,
				'user_id'	 => $this->getUserId()
			);
			$sql = ' INSERT INTO ' . $this->getBreederTable() . $this->db->sql_build_array('INSERT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$this->triggerAdvancedPointsSystemAction('INSERT_BREEDER_RECORD', $breeder_id);
			$breeder_id = $this->getBreederId($name);
			$json = (object) [
				'saved' => $result,
				'id' => $breeder_id,
				'data' => $sql_ary
			];
			$this->processUploads($breeder_id, 0);
			return $json;
		}
	}

	function getBreederId($name) {
		if ($this->canRead()) {
			$sql = ' SELECT breeder_id AS id FROM ' . $this->getBreederTable() . 
			' WHERE breeder_name ="' . $this->db->sql_escape($name) . '"';
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$id = $row['id'];
			$this->db->sql_freeresult($result);
			return $id;	
		}
	}
	
	function seedRecordExists($seed_id) {
		return $this->getSeedRecord($seed_id) != null;
	}

	function updateSeedRecord($seed_id) {
		if ($this->canEdit()) {
			$seed_name = $this->request->variable('seed_name', '');
			$breeder_id = $this->request->variable('breeder_id', 0);
			$sql_ary = $this->buildSqlArrayFromSeedFormRequest($seed_name, $breeder_id);
			$sql = ' UPDATE ' . $this->getSeedsTable() . ' SET ' . 
			$this->db->sql_build_array('UPDATE', $sql_ary) . 
			' WHERE seed_id =' . $seed_id;
			$this->db->sql_query($sql);
			return $this->getSeedRecord($seed_id);
		}
	}

	function processGridDelete() {
		$seed_id = $this->request->variable('seed_id', 0);
		if ($this->canDelete()) {
			$this->deleteSeedRecord($seed_id);
			$this->triggerAdvancedPointsSystemAction('DELETE_SEED_RECORD', $seed_id);
		}
		return $this->getSeedRecord($seed_id);
	}
	
	function deleteSeedRecord($seed_id) {
		return $this->db->sql_query('DELETE FROM ' . $this->getSeedsTable() . ' WHERE seed_id = ' . $seed_id);
	}

	function getTotalRecordCount() {
		if ($this->canRead()) {
			$sql = ' SELECT count(*) AS count FROM ' . $this->getSeedsTable();
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$count = $row['count'];
			$this->db->sql_freeresult($result);
			return $count;
		}
	}

	function getAllowedExtensions() {
		return self::ALLOWED_EXTENSIONS;		
	}

	function getBaseDir() {
		return self::UPLOAD_BASE . '/';
	}

	function getTempDir() {
		return $this->phpbb_root_path . self::UPLOAD_TEMP . '/';
	}

	function getSeedsTable() {
		return 	$this->getDbPrefix().self::TABLE_SEEDS;	
	}

	function getBreederTable() {
		return $this->getDbPrefix().self::TABLE_BREEDER;
	}

	function getUploadsTable() {
		return $this->getDbPrefix().self::TABLE_UPLOADS;
	}

	function getAwardTable() {
		return $this->getDbPrefix().self::TABLE_AWARD;
	}

	function getEffectTable() {
		return $this->getDbPrefix().self::TABLE_EFFECT;
	}

	function getTasteTable() {
		return $this->getDbPrefix().self::TABLE_TASTE;
	}

	function getGeneticsTable() {
		return $this->getDbPrefix().self::TABLE_GENETICS;
	}

	function getMetaTagTable() {
		return $this->getDbPrefix().self::TABLE_META_TAG;
	}

	function getParentTable() {
		return $this->getDbPrefix().self::TABLE_PARENT;
	}

	function getSmellTable() {
		return $this->getDbPrefix().self::TABLE_SMELL;
	}

	function getUpperCaseCharOption($value) {
		if (strlen($value) >= 1) {
			return strtoupper(substr($value, 0, 1));
		}
	}

	function getUserId() {
		return $this->user->data['user_id'];
	}

	function isEnabled() {
		return (bool)$this->config['minty_seeds_enabled'];
	}

	function isUserEnabled() {
		return (bool)$this->user->data['user_minty_seeds_enabled'];
	}

	function isUserSplitEnabled() {
		return (bool)$this->user->data['user_minty_seeds_split_enabled'];
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

	function canRead() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('u_minty_seeds_read'));
	}

	function canEdit() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('u_minty_seeds_edit'));
	}

	function canDelete() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('u_minty_seeds_delete'));
	}

	function canAddBreeder() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('m_minty_seeds_add_breeder'));
	}

	function canEditBreeder() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('m_minty_seeds_edit_breeder'));
	}

	function canDeleteBreeder() {
		return $this->isAdmin() || (bool)($this->auth->acl_get('m_minty_seeds_delete_breeder'));
	}

}
