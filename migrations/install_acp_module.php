<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace minty\seeds\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['minty_seeds_goodbye']);
	}

	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_data()
	{
		return array(
			// array('config.add', array('minty_seeds_goodbye', 0)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_SEEDS_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_SEEDS_TITLE',
				array(
					'module_basename'	=> '\minty\seeds\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
