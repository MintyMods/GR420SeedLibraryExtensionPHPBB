<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
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
	'ACL_A_MINTY_SEEDS_ADMIN'	=> '<strong>Seed Library - </strong>Permission to <strong>Administer</strong></strong> the database',
	'ACL_U_MINTY_SEEDS_ADD'	=> '<strong>Seed Library - </strong>Permission to <strong>Add</strong> Records to the database',
	'ACL_M_MINTY_SEEDS_ADD_BREEDER'	=> '<strong>Seed Library - </strong>Permission to <strong>Add Breeder</strong> Records to the database',
	'ACL_U_MINTY_SEEDS_EDIT'	=> '<strong>Seed Library - </strong>Permission to <strong>Edit</strong> Records in the database',
	'ACL_M_MINTY_SEEDS_EDIT_BREEDER'	=> '<strong>Seed Library - </strong>Permission to <strong>Edit Breeder</strong> Records in the database',
	'ACL_U_MINTY_SEEDS_DELETE'	=> '<strong>Seed Library - </strong>Permission to <strong>Delete</strong> Records from the database',
	'ACL_M_MINTY_SEEDS_DELETE_BREEDER'	=> '<strong>Seed Library - </strong>Permission to <strong>Delete Breeder</strong> Records from the database',
	'ACL_U_MINTY_SEEDS_READ'	=> '<strong>Seed Library - </strong>Permission to <strong>Read</strong> Records / Search the database',
	'ACL_CAT_MINTY'		=> 'Minty Mods',
));

