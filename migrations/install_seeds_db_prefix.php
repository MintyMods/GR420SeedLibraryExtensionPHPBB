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

class install_seeds_db_prefix extends \phpbb\db\migration\migration {

	public function effectively_installed()	{
		return $this->config->offsetExists('minty_seeds_db_prefix');
	}

	public static function depends_on()	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_data() {
		return array(
			array('config.add', array('minty_seeds_db_prefix', 'phpbb_')),
		);
	}
}

