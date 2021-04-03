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
define("TABLE_SEEDS",  "minty_sl_seeds");
define("TABLE_BREEDER", "minty_sl_breeder");
define("TABLE_GENETICS", "minty_sl_genetics");
define("TABLE_SMELLS", "minty_sl_smells");
define("TABLE_EFFECTS", "minty_sl_effects");
define("TABLE_TASTES", "minty_sl_tastes");
define("TABLE_META_TAGS", "minty_sl_meta_tags");
define("TABLE_AWARDS", "minty_sl_awards");
define("TABLE_SEED",  "minty_sl_seed");
define("TABLE_SMELL", "minty_sl_smell");
define("TABLE_EFFECT", "minty_sl_effect");
define("TABLE_TASTE", "minty_sl_taste");
define("TABLE_META_TAG", "minty_sl_meta_tag");
define("TABLE_AWARD", "minty_sl_award");

class main_controller {

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
		// $this->language->add_lang('extended_common', 'phpbbstudio/extended');
		$json = null;
		if ($name == 'GRID_SELECT_RECORDS') {
			$json = $this->getGridSelect();	
		} else if ($name == 'minty_sl_seeds') {
			$json = $this->processSeedFormPost();		
		} else if ($name == 'BREEDER_UPLOAD') {
			// $json = $this->processFileUpload();		
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
		$json_response->send($json);
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

	function processSeedFormPost() {
		$seed_id = $this->request->variable('seed_id', 0);
		$this->processComboPostedOptions($seed_id);
		if ($this->seedRecordExists($seed_id)) {
			$this->triggerAdvancedPointsSystemAction('UPDATE_SEED_RECORD', $seed_id);
			return $this->updateSeedRecord($seed_id);
		} else {
			$this->triggerAdvancedPointsSystemAction('INSERT_SEED_RECORD', $seed_id);
			return $this->insertNewSeedRecord();
		}
	}

	function getTablePrefixFromComboName($name) {
		$prefix = str_replace('minty_sl_', '', $name); 
		$prefix = substr($prefix, 0, strlen($prefix) - 1);
		return $prefix;
	}

	function processComboPostedOptions($seed_id) {		
		if ($this->canEdit() || $this->canAdd()) {
			$this->processComboOptions('minty_sl_genetics', $seed_id);
			$this->processComboOptions('minty_sl_awards', $seed_id);
			$this->processComboOptions('minty_sl_smells', $seed_id);
			$this->processComboOptions('minty_sl_tastes', $seed_id);
			$this->processComboOptions('minty_sl_effects', $seed_id);
			$this->processComboOptions('minty_sl_meta_tags', $seed_id);
		}
	}

	function deleteExistingComboRecords($name, $seed_id) {
		if ($this->canDelete()) {
			return $this->db->sql_query('DELETE FROM ' . TABLE_PREFIX . $name . ' WHERE seed_id = ' . $seed_id);
		}
	}

	function insertNewComboRecord($name, $seed_id, $value) {
		if ($this->canAdd()) {
			$prefix = $this->getTablePrefixFromComboName($name);
			$sql_ary = array(
				'seed_id'		=> $seed_id,
				$prefix . '_id'	=> $this->parseComboValue($name, $seed_id, $value, $prefix)
			);
			$sql = ' INSERT INTO ' . TABLE_PREFIX . $name . $this->db->sql_build_array('INSERT', $sql_ary);
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
			$parsed = $this->db->sql_escape(substr($tag, (strpos($tag, ']') + 1), strlen($tag)));
			$sql_ary = array(
				$prefix . '_name'	=> $parsed,
				$prefix . '_desc'	=> '** added dynamically by seed id ' . $seed_id . ' **',
			);
			$sql = ' INSERT INTO ' . TABLE_PREFIX . 'minty_sl_'.$prefix . 
				$this->db->sql_build_array('INSERT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$this->db->sql_freeresult($result);
			return $this->getComboTagId($table, $seed_id, $parsed, $prefix);
		}
	}

	function getComboTagId($table, $seed_id, $value, $prefix) {
		if ($this->canRead()) {
			$table = substr($table, 0, strlen($table)-1);
			$sql = ' SELECT ' . $prefix . '_id FROM ' . TABLE_PREFIX . $table . 
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
			$sql = ' SELECT * FROM ' . TABLE_PREFIX.TABLE_BREEDER;
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
			$sql = ' INSERT INTO ' . TABLE_PREFIX.TABLE_SEEDS . $this->db->sql_build_array('INSERT', $sql_ary);
			if ($this->db->sql_query($sql)) {
				return $this->getSeedRecord($this->getSeedIdFromNameAndBreeder($seed_name, $breeder_id));
			}
		}
	}

	function getSeedRecord($seed_id) {
		if ($this->canRead()) {
			$sql = ' SELECT * FROM ' . TABLE_PREFIX.TABLE_SEEDS . ' WHERE seed_id = ' . $this->db->sql_escape($seed_id);
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
			'flowering_type'	=> $this->db->sql_escape($this->request->variable('flowering_type', '')),
			'sex'				=> $this->db->sql_escape($this->request->variable('sex', '')),
			'indoor_yn'			=> $this->db->sql_escape((int)($this->request->variable('indoor_yn','') == 'true')),
			'outdoor_yn'		=> $this->db->sql_escape((int)($this->request->variable('outdoor_yn', '') == 'true')),
			'thc'				=> $this->db->sql_escape($this->request->variable('thc', '')),
			'cbd'				=> $this->db->sql_escape($this->request->variable('cbd', '')),
			'indica'			=> $this->db->sql_escape($this->request->variable('indica', '')),
			'sativa'			=> $this->db->sql_escape($this->request->variable('sativa', '')),
			'ruderalis'			=> $this->db->sql_escape($this->request->variable('ruderalis', '')),
			'yeild_indoors'		=> $this->db->sql_escape($this->request->variable('yeild_indoors', '')),
			'yeild_outdoors'	=> $this->db->sql_escape($this->request->variable('yeild_outdoors', '')),
			'height_indoors'	=> $this->db->sql_escape($this->request->variable('height_indoors', '')),
			'height_outdoors'	=> $this->db->sql_escape($this->request->variable('height_outdoors', '')),
			'flowering_time'	=> $this->db->sql_escape($this->request->variable('flowering_time', '')),
			'harvest_month'		=> $this->db->sql_escape($this->request->variable('harvest_month', '')),
			'seed_desc'			=> $this->db->sql_escape($this->request->variable('seed_desc', '')),
		);	
		return $sql_ary;	
	}

	function getSeedIdFromNameAndBreeder($name, $breeder) {
		if ($this->canRead()) {
			$sql = ' SELECT seed_id FROM ' . TABLE_PREFIX.TABLE_SEEDS . ' WHERE breeder_id = ' . 
				$this->db->sql_escape($breeder) . 
				' AND seed_name = "' . $this->db->sql_escape($name) . '"';
			$result = $this->db->sql_query($sql);
			if ($row = $this->db->sql_fetchrow($result)) {
				$this->db->sql_freeresult($result);
				return $row['seed_id'];
			}
		}
	}

	function getSmellsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT * FROM ' . TABLE_PREFIX.TABLE_SMELL;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$result_list[] = array(
					'id'	=> $row['smell_id'],
					'value'	=> $row['smell_name'],
				);
			}
			$this->db->sql_freeresult($result);
		}
		return $result_list;
	}

	function geEffectsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT * FROM ' . TABLE_PREFIX.TABLE_EFFECT;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$result_list[] = array(
					'id'	=> $row['effect_id'],
					'value'	=> $row['effect_name'],
				);
			}
			$this->db->sql_freeresult($result);
		}
		return $result_list;
	}

	function getTastesOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT * FROM ' . TABLE_PREFIX.TABLE_TASTE;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$result_list[] = array(
					'id'	=> $row['taste_id'],
					'value'	=> $row['taste_name'],
				);
			}
			$this->db->sql_freeresult($result);
		}
		return $result_list;
	}

	function getMetaTagsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT * FROM ' . TABLE_PREFIX.TABLE_META_TAG;	
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$result_list[] = array(
					'id'	=> $row['meta_tag_id'],
					'value'	=> $row['meta_tag_name'],
				);
			}
			$this->db->sql_freeresult($result);
		}
		return $result_list;
	}

	function getAwardsOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT * FROM ' . TABLE_PREFIX.TABLE_AWARD;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$result_list[] = array(
					'id'	=> $row['award_id'],
					'value'	=> $row['award_name'],
				);
			}
			$this->db->sql_freeresult($result);
		}
		return $result_list;
	}

	function getBreederOptions() {
		if ($this->canRead()) {
			$sql = ' SELECT breeder_id AS id, breeder_name AS value FROM ' . TABLE_PREFIX.TABLE_BREEDER;
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
					' FROM ' . TABLE_PREFIX.TABLE_SEEDS . ' S, ' . TABLE_PREFIX.TABLE_BREEDER . ' B' .
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
					TABLE_GENETICS			=> $this->getComboGeneticOptions($seed_id),
					TABLE_SMELLS			=> $this->getComboOptions(TABLE_SMELLS, $seed_id),
					TABLE_EFFECTS			=> $this->getComboOptions(TABLE_EFFECTS, $seed_id),
					TABLE_TASTES			=> $this->getComboOptions(TABLE_TASTES, $seed_id),
					TABLE_META_TAGS			=> $this->getComboOptions(TABLE_META_TAGS, $seed_id),
					TABLE_AWARDS			=> $this->getComboOptions(TABLE_AWARDS, $seed_id),
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
			$sql = ' SELECT ' . $name . '_id AS id FROM ' . TABLE_PREFIX.$table . ' WHERE seed_id = ' . $seed_id;
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
				' FROM ' . TABLE_PREFIX.TABLE_SEEDS . ' S, ' . TABLE_PREFIX.TABLE_BREEDER . ' B' .
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
				' FROM ' . TABLE_PREFIX.TABLE_SEEDS . ' S, ' . TABLE_PREFIX.TABLE_BREEDER . ' B' .
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
			$sql = ' SELECT genetic_id FROM ' . TABLE_PREFIX.TABLE_GENETICS . ' WHERE seed_id = ' . $seed_id;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))	{
				$descriptions[] = $row['genetic_id'];
			}
			$this->db->sql_freeresult($result);
			return $descriptions;
		}
	}

	function processFileUpload() {
		// $filespec = $this->file_factory->get('files.filespec');
		// $upload = $this->file_factory->get('files.upload')->set_allowed_extensions(array('jpg', 'jpeg', 'gif', 'png'));
		// $upload_dir = $this->phpbb_root_path . 'minty_uploads';
		// $files = $upload->handle_upload('files.types.form', 'file_upload');
		// $upload_file = $this->request->file('breeder_logo');
		// if (!empty($upload_file['breeder_logo'])) {
		// 	$file = $upload->handle_upload('files.types.form', 'breeder_logo');
		// }
		// $this->triggerAdvancedPointsSystemAction('IMAGE_UPLOAD', $breeder_id);
	}

	function processBreederFormPost() {
		// $this->triggerAdvancedPointsSystemAction('EDIT_BREEDER_RECORD', $breeder_id);
		// $this->triggerAdvancedPointsSystemAction('DELETE_BREEDER_RECORD', $breeder_id);
		if ($this->canAddBreeder()) {
			$breeder_id = $this->request->variable('breeder_id', 0);
			$name = $this->request->variable('breeder_name', '');
			$desc = $this->request->variable('breeder_desc', '');
			$url = $this->request->variable('breeder_url', '');
			// $logo = $this->request->variable('breeder_logo', array());
			$sponsor = $this->request->variable('sponsor_yn', 'false') == 'true';
			$sql_ary = array(
				'breeder_name'	=> $this->db->sql_escape($name),
				'breeder_desc'	=> $this->db->sql_escape($desc),
				'breeder_url'	=> $this->db->sql_escape($url),
				'sponsor_yn'	=> $sponsor,
			);
			$sql = ' INSERT INTO ' . TABLE_PREFIX.TABLE_BREEDER . $this->db->sql_build_array('INSERT', $sql_ary);
			$result = $this->db->sql_query($sql);
			$this->triggerAdvancedPointsSystemAction('INSERT_BREEDER_RECORD', $breeder_id);
			$json = (object) [
				'saved' => $result,
				'id' => $this->getBreederId($name),
				'data' => $sql_ary
			];
			$this->db->sql_freeresult($result);
			return $json;
		}
	}

	function getBreederId($name) {
		if ($this->canRead()) {
			$sql = ' SELECT breeder_id AS id FROM ' . TABLE_PREFIX.TABLE_BREEDER . 
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
			$sql = ' UPDATE ' . TABLE_PREFIX.TABLE_SEEDS . ' SET ' . 
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
		return $this->db->sql_query('DELETE FROM ' . TABLE_PREFIX.TABLE_SEEDS . ' WHERE seed_id = ' . $seed_id);
	}

	function getTotalRecordCount() {
		if ($this->canRead()) {
			$sql = ' SELECT count(*) AS count FROM ' . TABLE_PREFIX.TABLE_SEEDS;
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$count = $row['count'];
			$this->db->sql_freeresult($result);
			return $count;
		}
	}

	function getUpperCaseCharOption($value) {
		if (strlen($value) >= 1) {
			return strtoupper(substr($value, 0, 1));
		}
	}

	function isEnabled() {
		return (bool)$this->config['minty_seeds_enabled'];
	}
	function isUserEnabled() {
		return (bool)$this->user->data['user_minty_seeds_enabled'];
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
