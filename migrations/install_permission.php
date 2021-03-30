<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace minty\seeds\migrations;

class install_permission extends \phpbb\db\migration\migration {
	static public function depends_on() {
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_data() {
		return array(

			array('permission.add', array('a_minty_seeds')),
			array('permission.add', array('m_minty_seeds')),
			array('permission.add', array('u_minty_seeds')),

			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_minty_seeds')), 
			array('permission.permission_set', array('ROLE_MOD_FULL', 'm_minty_seeds')), 
			array('permission.permission_set', array('ROLE_USER_FULL', 'u_minty_seeds')), 
			array('permission.permission_set', array('ROLE_USER_STANDARD', 'u_minty_seeds')),
			array('permission.permission_set', array('REGISTERED', 'u_minty_seeds', 'group')),
			array('permission.permission_set', array('REGISTERED_COPPA', 'u_minty_seeds', 'group', false)),

		);
	}
}
