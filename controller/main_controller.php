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

/**
 * Minty Seed Library main controller.
 */
class main_controller
{
	protected $request;
	protected $config;
	protected $helper;
	protected $template;
	protected $language;
	protected $db;
	protected $log;
	protected $php_ext;
	protected $phpbb_root_path;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config		Config object
	 * @param \phpbb\controller\helper	$helper		Controller helper object
	 * @param \phpbb\template\template	$template	Template object
	 * @param \phpbb\language\language	$language	Language object
	 */
	public function __construct(\phpbb\request\request $request, 
								\phpbb\config\config $config, 
								\phpbb\controller\helper $helper, 
								\phpbb\template\template $template, 
								\phpbb\language\language $language,
								\phpbb\db\driver\factory $dbal,
								\phpbb\log\log $log,
								$phpbb_root_path, 
								$phpEx
								)
	{
		$this->request = $request;	
		$this->config	= $config;
		$this->helper	= $helper;
		$this->template	= $template;
		$this->language	= $language;
		$this->db = $dbal;
		$this->log	= $log;
		$this->php_ext = $phpEx;
		$this->phpbb_root_path = $phpbb_root_path;			
	}

	/**
	 * Controller handler for route /demo/{name}
	 *
	 * @param string $name
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($name)
	{

		if ($name == 'json') {
			$json_response = new \phpbb\json_response();
			return $json_response->send($this->getJson());
		} else if ($name == 'SEED_POST') {
			return $this->processSeedFormPost($this->$request);		
		} else if ($name == 'BREEDER_POST') {
			return $this->processBreederFormPost($this->$request);		
		} else if ($name == 'GRID_SELECT') {
			return $this->getGridSelectJson();		
		} else if ($name == 'world') {
			$l_message = !$this->config['minty_seeds_goodbye'] ? 'SEEDS_HELLO' : 'SEEDS_GOODBYE';
			$this->template->assign_var('SEEDS_MESSAGE', $this->language->lang($l_message, $name));
			return $this->helper->render('@minty_seeds/seeds_body.html', $name);
		} 

	}

	function processSeedFormPost($request) {
		$seed_id = $this->request->variable('seed_id', 0);// 0 for int ' '  for string	
		$seed_name = $this->request->variable('seed_name', '');// 0 for int ' '  for string
		// var_dump($this->request); 
		// echo "Hello world!";		
	}
	function processBreederFormPost($request) {
		$breeder_id = $this->request->variable('breeder_id', 0);// 0 for int ' '  for string	
		$breeder_name = $this->request->variable('breeder_name', '');// 0 for int ' '  for string
		$breeder_desc = $this->request->variable('breeder_desc', '');// 0 for int ' '  for string
		$breeder_url = $this->request->variable('breeder_url', '');// 0 for int ' '  for string
		$sponsor_yn = $this->request->variable('sponsor_yn', '');// 0 for int ' '  for string
		 var_dump($this->request); 
		// echo "Hello world!";		
	}

	function getGridSelectJson() {
		require("./config.php"); 
		$this->request->enable_super_globals();

		$from = request_var('from', 0);
		$limit = request_var('limit', 0);
		$seed_list = array();
		$sql = ' SELECT S.seed_id, S.seed_name, B.breeder_name,' . 
				    'S.flowering_type, S.sex, S.indoor_yn, S.outdoor_yn,' . 
					'S.flowering_time,	S.harvest_month, S.thc, S.cbd, ' .
					'S.indica, S.sativa, S.ruderalis, S.yeild_indoors, S.yeild_outdoors,' .
					'S.height_indoors, S.height_outdoors, S.vote_likes,' .
					'S.vote_dislikes, S.seed_desc, S.forum_url' . 
				' FROM phpbb_minty_sl_seeds S, phpbb_minty_sl_breeder B' .
				' WHERE S.breeder_id = B.breeder_id'  . 
				' AND S.seed_id >= ' . $from . ' AND S.seed_id <= ' . ($from + $limit);
			// @todo remove phpbb prefix

		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))	{
			$seed_list[] = array(
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

		$json = (object) [
			'data' => $seed_list,
			'total_count' => $this->getTotalRecordCount(),
			'from' => $from
		];

		$this->db->sql_freeresult($result);
		$json_response = new \phpbb\json_response();
		$json_response->send($json);


		// @todo
		// { width: 200, id: "minty_sl_genetics", header: [{ text: "Genetics" }] },
		// { width: 200, id: "minty_sl_smells", header: [{ text: "Smells" }] },
		// { width: 200, id: "minty_sl_tastes", header: [{ text: "Tastes" }] },
		// { width: 200, id: "minty_sl_effects", header: [{ text: "Effects" }] },
		// { width: 200, id: "minty_sl_meta_tag", header: [{ text: "Effects" }] },


	}

	function getTotalRecordCount() {
		//@todo 
		return 29;
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

	function getJson() {
		require("./config.php"); 
		require("dhtmlx/grid_connector.php");
		$this->request->enable_super_globals();
		
		// var_dump($this->request); 
		// echo "Hello world!";
		$res = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpasswd);
		// $connector = new GridConnector($res);
		// $connector->enable_log($this->log_file);
		// $connector->render_table($this->table_name,"id","start_date,end_date,text,sponsor,status");

	}


}
