<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace minty\seeds\acp;

/**
 * Minty Seed Library ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\minty\seeds\acp\main_module',
			'title'		=> 'ACP_SEEDS_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_SEEDS_SETTINGS',
					'auth'	=> 'ext_minty/seeds && acl_a_minty_seeds_admin',
					'cat'	=> array('ACP_MINTY_MODS')
				),
			),
		);
	}
}
