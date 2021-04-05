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

class update_seeds_audit extends \phpbb\db\migration\migration {

	public function effectively_installed() {
		return $this->db_tools->sql_column_exists($this->table_prefix . 'minty_sl_seeds', 'user_id');
	}

	public static function depends_on() {
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_schema() 	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'minty_sl_seeds'	=> array(
					'user_id' => array('UINT'),
				),
				$this->table_prefix . 'minty_sl_breeder'	=> array(
					'user_id' => array('UINT'),
				),
				$this->table_prefix . 'minty_sl_genetics'	=> array(
					'user_id' => array('UINT'),
				),
				$this->table_prefix . 'minty_sl_smells'	=> array(
					'user_id' => array('UINT'),
				),
				$this->table_prefix . 'minty_sl_tastes'	=> array(
					'user_id' => array('UINT'),
				),
				$this->table_prefix . 'minty_sl_effects'	=> array(
					'user_id' => array('UINT'),
				),
				$this->table_prefix . 'minty_sl_meta_tags'	=> array(
					'user_id' => array('UINT'),
				),
				$this->table_prefix . 'minty_sl_awards'	=> array(
					'user_id' => array('UINT'),
				),
			),
		);
	}

}
