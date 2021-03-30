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
	'ACP_MINTY_MODS' => 'Minty Mods',
	'ACP_SEEDS_SETTING_SAVED'	=> 'Settings have been saved successfully!',
	'SEEDS_PAGE'				=> 'Seeds',
	'VIEWING_MINTY_SEEDS'		=> 'Viewing Minty Seed Library page',
));
