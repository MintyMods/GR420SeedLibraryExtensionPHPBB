<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace minty\seeds\controller;

class ucp_controller {

	protected $db;
	protected $language;
	protected $request;
	protected $template;
	protected $user;
	protected $u_action;

	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user) {
		$this->db		= $db;
		$this->language	= $language;
		$this->request	= $request;
		$this->template	= $template;
		$this->user		= $user;
	}

	public function display_options() {
		add_form_key('minty_seeds_ucp');
		$errors = array();
		$data = array(
			'user_minty_seeds_enabled' => $this->request->variable('user_minty_seeds_enabled', $this->user->data['user_minty_seeds_enabled']),
		);

		if ($this->request->is_set_post('submit')) {
			if (!check_form_key('minty_seeds_ucp'))	{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			if (empty($errors)) {
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $this->db->sql_build_array('UPDATE', $data) . '
					WHERE user_id = ' . (int) $this->user->data['user_id'];
				$this->db->sql_query($sql);

				meta_refresh(3, $this->u_action);
				$message = $this->language->lang('UCP_SEEDS_SAVED') . '<br /><br />' . $this->language->lang('RETURN_UCP', '<a href="' . $this->u_action . '">', '</a>');
				trigger_error($message);
			}
		}

		$s_errors = !empty($errors);

		$this->template->assign_vars(array(
			'S_ERROR'		=> $s_errors,
			'ERROR_MSG'		=> $s_errors ? implode('<br />', $errors) : '',
			'U_UCP_ACTION'	=> $this->u_action,
			'USER_MINTY_SEEDS_ENABLED'	=> $data['user_minty_seeds_enabled'],
		));
	}

	public function set_page_url($u_action) {
		$this->u_action = $u_action;
	}
}
