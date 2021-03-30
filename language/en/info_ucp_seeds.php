<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'UCP_SEEDS'				=> 'Settings',
	'UCP_SEEDS_TITLE'		=> 'Minty Seed Library Module',
	'UCP_SEEDS_USER'			=> 'Minty Seed Library user',
	'UCP_SEEDS_USER_EXPLAIN'	=> 'User permissions for the Seed Library',
	'UCP_SEEDS_SAVED'		=> 'Settings have been saved successfully!',
));
