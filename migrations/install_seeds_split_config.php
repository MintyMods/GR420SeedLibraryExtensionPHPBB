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

class install_seeds_split_config extends \phpbb\db\migration\migration
{
	public function effectively_installed() {
		return $this->db_tools->sql_column_exists($this->table_prefix . 'users', 'user_minty_seeds_split_enabled');
	}

	public static function depends_on() {
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_schema()	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_minty_seeds_split_enabled'	=> array('UINT', 0),
				),
			),
		);
	}

	public function revert_schema() {
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_minty_seeds_split_enabled',
				),
			),
		);
	}
}
