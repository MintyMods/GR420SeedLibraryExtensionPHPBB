<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace minty\seeds\ucp;

class main_info {
	public function module() {
		return array(
			'filename'	=> '\minty\seeds\ucp\main_module',
			'title'		=> 'UCP_SEEDS_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'UCP_SEEDS_TITLE',
					'auth'	=> 'ext_minty/seeds && acl_u_minty_seeds_read',
					'cat'	=> array('UCP_SEEDS_TITLE')
				),
			),
		);
	}
}
