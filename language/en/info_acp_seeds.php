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
	'ACP_SEEDS_TITLE'	=> 'Minty Seed Library',
	'ACP_SEEDS'			=> 'Seed Library Settings',
	'ACP_MINTY_SEEDS_ENABLED'	=> 'Seed Library Enabled',
	'LOG_ACP_SEEDS_SETTINGS'		=> '<strong>Seed Library settings updated</strong>',
));
