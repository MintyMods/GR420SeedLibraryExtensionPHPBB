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

class install_seeds_upload extends \phpbb\db\migration\migration {

	public function effectively_installed() {
		return $this->db_tools->sql_table_exists($this->table_prefix . 'minty_sl_uploads');
	}

	public static function depends_on() {
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_schema() 	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'minty_sl_uploads'	=> array(
					'COLUMNS'		=> array(
						'id'		=> array('UINT', null, 'auto_increment'),
						'seed_id'	=> array('UINT',0),
						'breeder_id'=> array('UINT',0),
						'user_id'	=> array('UINT',0),
						'size'		=> array('UINT',0),
						'upload_id'	=> array('VCHAR_UNI:255',''),
						'name'		=> array('VCHAR_UNI:255',''),
						'filename'	=> array('VCHAR_UNI:255',''),
						'realname'	=> array('VCHAR_UNI:255',''),
						'uploadname'=> array('VCHAR_UNI:255',''),
						'extension'	=> array('VCHAR_UNI:255',''),
						'type'		=> array('VCHAR_UNI:255',''),
						'path'		=> array('VCHAR_UNI:255',''),
						'preview'	=> array('VCHAR_UNI:255',''),
						'status'	=> array('VCHAR_UNI:255',''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
				$this->table_prefix . 'minty_sl_upload'	=> array(
					'COLUMNS'		=> array(
						'id'		=> array('UINT', null, 'auto_increment'),
						'upload_id'	=> array('VCHAR_UNI:255',''),
						'seed_id'	=> array('UINT'),
						'breeder_id'	=> array('UINT'),
						'user_id'	=> array('UINT'),
					),
					'PRIMARY_KEY'	=> 'id',
				),				
			),

		);
	}

	public function revert_schema() {
		return array(
			'drop_tables'		=> array(
				$this->table_prefix . 'minty_sl_uploads',				
				$this->table_prefix . 'minty_sl_upload',				
			),
		);
	}
}
