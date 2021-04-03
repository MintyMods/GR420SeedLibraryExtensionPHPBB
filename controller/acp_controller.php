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

class acp_controller {

	protected $config;
	protected $language;
	protected $log;
	protected $request;
	protected $template;
	protected $user;
	protected $u_action;

	public function __construct(\phpbb\config\config $config, \phpbb\language\language $language, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user) {
		$this->config	= $config;
		$this->language	= $language;
		$this->log		= $log;
		$this->request	= $request;
		$this->template	= $template;
		$this->user		= $user;
	}

	public function display_options() {

		$this->language->add_lang('common', 'minty/seeds');
		add_form_key('minty_seeds_acp');
		$errors = array();
		if ($this->request->is_set_post('submit')) {

			if (!check_form_key('minty_seeds_acp'))	{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			if (empty($errors))	{
				$this->config->set('minty_seeds_enabled', $this->request->variable('minty_seeds_enabled', 0));
				$this->config->set('minty_seeds_debug', $this->request->variable('minty_seeds_debug', 0));
				$this->config->set('minty_seeds_title', $this->request->variable('minty_seeds_title', ''));
				$this->config->set('minty_seeds_aps_enabled', $this->request->variable('minty_seeds_aps_enabled', 0));
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_SEEDS_SETTINGS');
				trigger_error($this->language->lang('ACP_SEEDS_SETTING_SAVED') . adm_back_link($this->u_action));
			}
		}

		$s_errors = !empty($errors);

		$this->template->assign_vars(array(
			'S_ERROR'		=> $s_errors,
			'ERROR_MSG'		=> $s_errors ? implode('<br />', $errors) : '',
			'U_ACTION'		=> $this->u_action,
			'MINTY_SEEDS_ENABLED'	=> (bool) $this->config['minty_seeds_enabled'],
			'USER_MINTY_SEEDS_ENABLED'	=> (bool) $this->user->data['user_minty_seeds_enabled'],
			'MINTY_SEEDS_APS_ENABLED'	=> (bool) $this->config['minty_seeds_aps_enabled'],
			'MINTY_SEEDS_DEBUG' => (bool) $this->config['minty_seeds_debug'],
			'MINTY_SEEDS_VERSION' => $this->config['minty_seeds_version'],				
			'MINTY_SEEDS_TITLE' => $this->config['minty_seeds_title'],
		));
	}

	public function set_page_url($u_action) {
		$this->u_action = $u_action;
	}
}
