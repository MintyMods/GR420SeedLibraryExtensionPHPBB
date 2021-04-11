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

class install_seeds_parent extends \phpbb\db\migration\migration {
	public function effectively_installed() {
		return $this->db_tools->sql_table_exists($this->table_prefix . 'minty_sl_parents');
	}

	public static function depends_on() {
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_schema() 	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'minty_sl_parents'	=> array(
					'COLUMNS'		=> array(
						'seed_id'				=> array('UINT'),
						'parent_id'				=> array('UINT'),
						'user_id' => array('UINT'),
					),
					'PRIMARY_KEY'	=> 'seed_id, parent_id',
				),

				$this->table_prefix . 'minty_sl_parent'	=> array(
					'COLUMNS'		=> array(
						'parent_id'				=> array('UINT', null, 'auto_increment'),
						'parent_name'			=> array('VCHAR_UNI:255', ''),
						'parent_desc'			=> array('TEXT_UNI', ''),
						'vote_likes'			=> array('USINT', 0),
						'vote_dislikes'			=> array('USINT', 0),
						'user_id' => array('UINT'),						
					),
					'PRIMARY_KEY'	=> 'parent_id',
				),
			)
		);
	}
	
	public function revert_schema() {
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'minty_sl_parent',				
				$this->table_prefix . 'minty_sl_parents',
			),
		);
	}
}
