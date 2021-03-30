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
	'ACL_A_MINTY_SEEDS'	=> 'Seed Library : Can use Administration features of the Seed Database',
	'ACL_M_MINTY_SEEDS'	=> 'Seed Library : Can Add/Edit/Delete Records from the Seed Database!',
	'ACL_U_MINTY_SEEDS'	=> 'Seed Library : ReadOnly - Can Search the Seed Database',
	'ACL_F_MINTY_SEEDS'	=> 'Seed Library : Forum permissions for the Seed Database',
	'ACL_CAT_MINTY'		=> 'Minty Mods',
));

