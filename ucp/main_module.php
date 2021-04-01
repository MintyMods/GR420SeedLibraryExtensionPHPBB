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

class main_module {
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode) {
		global $phpbb_container;
		$ucp_controller = $phpbb_container->get('minty.seeds.controller.ucp');
		$language = $phpbb_container->get('language');
		$this->tpl_name = 'ucp_seeds_body';
		$this->page_title = $language->lang('UCP_SEEDS_TITLE');
		$ucp_controller->set_page_url($this->u_action);
		$ucp_controller->display_options();
	}
}
