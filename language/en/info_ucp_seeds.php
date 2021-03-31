<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB')){
	exit;
}

if (empty($lang) || !is_array($lang)){
	$lang = array();
}

$lang = array_merge($lang, array(
	'UCP_SEEDS'				=> 'Minty Seed Library',
	'UCP_SEEDS_TITLE'		=> 'Minty Mods',
	'UCP_SEEDS_USER'			=> 'Seed Library Enabled',
	'UCP_SEEDS_USER_EXPLAIN'	=> 'Show/Hide the Seed Library ToolBar Icon',
	'UCP_SEEDS_SAVED'		=> 'Settings have been saved successfully!',
));
