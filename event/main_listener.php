<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace minty\seeds\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface {

	protected $auth;
	protected $user;
	protected $language;
	protected $helper;
	protected $template;
	protected $config;
	protected $php_ext;

	public static function getSubscribedEvents() 	{
		return array(
			'core.user_setup'	=> 'load_language_on_setup',
			'core.page_header'	=> 'add_page_header_link',
			'core.permissions'	=> 'permissions',
		);
	}

	public function __construct(\phpbb\auth\auth $auth, \phpbb\user $user, \phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\config\config $config, $php_ext) {		
		$this->auth = $auth;
		$this->user = $user;
		$this->language = $language;
		$this->helper   = $helper;
		$this->template = $template;
		$this->config = $config;
		$this->php_ext  = $php_ext;
	}

	public function load_language_on_setup($event) {
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'minty/seeds',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function add_page_header_link() {
		$this->template->assign_vars(array(
			'U_SEEDS_PAGE'	=> $this->helper->route('minty_seeds_controller', array('name' => 'main')),
			'MINTY_SEEDS_ENABLED' => (bool) $this->config['minty_seeds_enabled'],
			'USER_MINTY_SEEDS_ENABLED' => (bool) $this->user->data['user_minty_seeds_enabled'],
			'A_MINTY_SEEDS_ADMIN'	=> ($this->auth->acl_get('a_minty_seeds_admin')) ? true : false,
			'M_MINTY_SEEDS_ADD_BREEDER'	=> ($this->auth->acl_get('m_minty_seeds_add_breeder')) ? true : false,
			'M_MINTY_SEEDS_EDIT_BREEDER'	=> ($this->auth->acl_get('m_minty_seeds_edit_breeder')) ? true : false,
			'M_MINTY_SEEDS_DELETE_BREEDER'	=> ($this->auth->acl_get('m_minty_seeds_delete_breeder')) ? true : false,
			'U_MINTY_SEEDS_ADD'	=> ($this->auth->acl_get('u_minty_seeds_add')) ? true : false,
			'U_MINTY_SEEDS_EDIT'	=> ($this->auth->acl_get('u_minty_seeds_edit')) ? true : false,
			'U_MINTY_SEEDS_DELETE'	=> ($this->auth->acl_get('u_minty_seeds_delete')) ? true : false,
			'U_MINTY_SEEDS_READ'	=> ($this->auth->acl_get('u_minty_seeds_read')) ? true : false,
		));
	}

	public function permissions($event) {

		$categories = $event['categories'];
		 if (empty($categories['minty'])) {
			$categories['minty'] = 'ACL_CAT_MINTY';
			$event['categories'] = $categories;
		 }

		$permissions = $event['permissions'];
		$permissions['a_minty_seeds_admin'] = array('lang' => 'ACL_A_MINTY_SEEDS_ADMIN', 'cat' => 'minty');
		$permissions['u_minty_seeds_add'] = array('lang' => 'ACL_U_MINTY_SEEDS_ADD', 'cat' => 'minty');
		$permissions['u_minty_seeds_edit'] = array('lang' => 'ACL_U_MINTY_SEEDS_EDIT', 'cat' => 'minty');
		$permissions['u_minty_seeds_delete'] = array('lang' => 'ACL_U_MINTY_SEEDS_DELETE', 'cat' => 'minty');
		$permissions['u_minty_seeds_read'] = array('lang' => 'ACL_U_MINTY_SEEDS_READ', 'cat' => 'minty');

		$event['permissions'] = $permissions;
	}


}

