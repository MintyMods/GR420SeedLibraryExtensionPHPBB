<?php
/**
 *
 * phpBB Studio - Advanced Points System. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB')) {
	exit;
}

if (empty($lang) || !is_array($lang)) {
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_APS_MINTY_MODS_CATEGORY' => 'Seed Library',
	'MINTY_SEEDS_UPDATE_SEED_RECORD'		=> 'Update Seed Record',
	'MINTY_SEEDS_UPDATE_SEED_RECORD_DESC'	=> 'This will calculate %1$s when editing seed records in the seed library database.',
	'MINTY_SEEDS_INSERT_SEED_RECORD'		=> 'Insert Seed Record',
	'MINTY_SEEDS_INSERT_SEED_RECORD_DESC'	=> 'This will calculate %1$s when adding new seed records to the seed library database.',
	'MINTY_SEEDS_DELETE_SEED_RECORD'		=> 'Delete Seed Record',
	'MINTY_SEEDS_DELETE_SEED_RECORD_DESC'	=> 'This will calculate %1$s when removing seed records from the seed library database.',
	'MINTY_SEEDS_UPDATE_BREEDER_RECORD'		=> 'Update Breeder Record',
	'MINTY_SEEDS_UPDATE_BREEDER_RECORD_DESC'	=> 'This will calculate %1$s when editing breeder records in the seed library database.',
	'MINTY_SEEDS_INSERT_BREEDER_RECORD'		=> 'Insert Breeder Record',
	'MINTY_SEEDS_INSERT_BREEDER_RECORD_DESC'	=> 'This will calculate %1$s when adding new breeder records to the seed library database.',
	'MINTY_SEEDS_DELETE_BREEDER_RECORD'		=> 'Delete Breeder Record',
	'MINTY_SEEDS_DELETE_BREEDER_RECORD_DESC'	=> 'This will calculate %1$s when removing breeder records from the seed library database.',
));