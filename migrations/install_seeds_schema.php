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

class install_seeds_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed() {
		return $this->db_tools->sql_column_exists($this->table_prefix . 'users', 'user_minty_seeds_enabled');
	}

	public static function depends_on() {
		return array('\phpbb\db\migration\data\v320\v320');
	}

	/**
	 * Update database schema.
	 *
	 * https://area51.phpbb.com/docs/dev/3.2.x/migrations/schema_changes.html
	 *	add_tables: Add tables
	 *	drop_tables: Drop tables
	 *	add_columns: Add columns to a table
	 *	drop_columns: Removing/Dropping columns
	 *	change_columns: Column changes (only type, not name)
	 *	add_primary_keys: adding primary keys
	 *	add_unique_index: adding an unique index
	 *	add_index: adding an index (can be column:index_size if you need to provide size)
	 *	drop_keys: Dropping keys
	 *
	 * This sample migration adds a new column to the users table.
	 * It also adds an example of a new table that can hold new data.
	 *
	 * @return array Array of schema changes
	 */
	public function update_schema() 	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'minty_sl_seeds'	=> array(
					'COLUMNS'		=> array(
						'seed_id'				=> array('UINT', null, 'auto_increment'),
						'seed_name'				=> array('VCHAR_UNI:255', ''),
						'breeder_id'			=> array('UINT', 0), /* minty_sl_breeder */
						'flowering_type'		=> array('VCHAR_UNI:1', ''), /* A=Auto, P=Photo */
						'sex'					=> array('VCHAR_UNI:1', ''), /* R=Regular, F=Female */
						'indoor_yn'				=> array('BOOL', 0), 
						'outdoor_yn'			=> array('BOOL', 0),
						'flowering_time'		=> array('VCHAR_UNI:255'),
						'harvest_month'			=> array('VCHAR_UNI:3'), /* Jan, Feb, Mar, etc  */
						'thc'					=> array('VCHAR_UNI:255'),
						'cbd'					=> array('VCHAR_UNI:255'),
						'indica'				=> array('VCHAR_UNI:255'),
						'sativa'				=> array('VCHAR_UNI:255'),
						'ruderalis'				=> array('VCHAR_UNI:255'),
						'yeild_indoors'			=> array('VCHAR_UNI:255'),
						'yeild_outdoors'		=> array('VCHAR_UNI:255'),
						'height_indoors'		=> array('VCHAR_UNI:255'),
						'height_outdoors' 		=> array('VCHAR_UNI:255'),
						'vote_likes'			=> array('VCHAR_UNI:255'),
						'vote_dislikes'			=> array('VCHAR_UNI:255'),						
						'seed_desc'				=> array('TEXT_UNI'),
						'forum_url'				=> array('VCHAR_UNI:255'),
					),
					'PRIMARY_KEY'	=> 'seed_id',
				),

				$this->table_prefix . 'minty_sl_breeder'	=> array(
					'COLUMNS'		=> array(
						'breeder_id'			=> array('UINT', null, 'auto_increment'),
						'breeder_name'			=> array('VCHAR_UNI:255', ''),
						'breeder_desc'			=> array('TEXT_UNI', ''),
						'breeder_url'			=> array('TEXT_UNI', ''),
						'sponsor_yn'			=> array('BOOL', 0), 
						'vote_likes'			=> array('USINT', 0),
						'vote_dislikes'			=> array('USINT', 0),						
					),
					'PRIMARY_KEY'	=> 'breeder_id',
				),

				$this->table_prefix . 'minty_sl_smells'	=> array(
					'COLUMNS'		=> array(
						'seed_id'				=> array('UINT'),
						'smell_id'				=> array('UINT'),
					),
					'PRIMARY_KEY'	=> 'seed_id, smell_id',
				),

				$this->table_prefix . 'minty_sl_smell'	=> array(
					'COLUMNS'		=> array(
						'smell_id'				=> array('UINT', null, 'auto_increment'),
						'smell_name'			=> array('VCHAR_UNI:255', ''),
						'smell_desc'			=> array('TEXT_UNI', ''),
						'vote_likes'			=> array('USINT', 0),
						'vote_dislikes'			=> array('USINT', 0),						
					),
					'PRIMARY_KEY'	=> 'smell_id',
				),

				$this->table_prefix . 'minty_sl_tastes'	=> array(
					'COLUMNS'		=> array(
						'seed_id'				=> array('UINT'),
						'taste_id'				=> array('UINT'),
					),
					'PRIMARY_KEY'	=> 'seed_id, taste_id',
				),

				$this->table_prefix . 'minty_sl_taste'	=> array(
					'COLUMNS'		=> array(
						'taste_id'				=> array('UINT', null, 'auto_increment'),
						'taste_name'			=> array('VCHAR_UNI:255', ''),
						'taste_desc'			=> array('TEXT_UNI', ''),
						'vote_likes'			=> array('USINT', 0),
						'vote_dislikes'			=> array('USINT', 0),						
					),
					'PRIMARY_KEY'	=> 'taste_id',
				),

				$this->table_prefix . 'minty_sl_effects'	=> array(
					'COLUMNS'		=> array(
						'seed_id'				=> array('UINT'),
						'effect_id'				=> array('UINT'),
					),
					'PRIMARY_KEY'	=> 'seed_id, effect_id',
				),

				$this->table_prefix . 'minty_sl_effect'	=> array(
					'COLUMNS'		=> array(
						'effect_id'				=> array('UINT', null, 'auto_increment'),
						'effect_name'			=> array('VCHAR_UNI:255', ''),
						'effect_desc'			=> array('TEXT_UNI', ''),
						'vote_likes'			=> array('USINT', 0),
						'vote_dislikes'			=> array('USINT', 0),						
					),
					'PRIMARY_KEY'	=> 'effect_id',
				),

				$this->table_prefix . 'minty_sl_meta_tags'	=> array(
					'COLUMNS'		=> array(
						'seed_id'				=> array('UINT'),
						'meta_tag_id'				=> array('UINT'),
					),
					'PRIMARY_KEY'	=> 'seed_id, meta_tag_id',
				),

				$this->table_prefix . 'minty_sl_meta_tag'	=> array(
					'COLUMNS'		=> array(
						'meta_tag_id'				=> array('UINT', null, 'auto_increment'),
						'meta_tag_name'			=> array('VCHAR_UNI:255', ''),
						'meta_tag_desc'			=> array('TEXT_UNI', ''),
						'vote_likes'			=> array('USINT', 0),
						'vote_dislikes'			=> array('USINT', 0),						
					),
					'PRIMARY_KEY'	=> 'meta_tag_id',
				),

				$this->table_prefix . 'minty_sl_awards'	=> array(
					'COLUMNS'		=> array(
						'seed_id'				=> array('UINT'),
						'award_id'				=> array('UINT'),
					),
					'PRIMARY_KEY'	=> 'seed_id, award_id',
				),

				$this->table_prefix . 'minty_sl_award'	=> array(
					'COLUMNS'		=> array(
						'award_id'				=> array('UINT', null, 'auto_increment'),
						'award_name'			=> array('VCHAR_UNI:255', ''),
						'award_desc'			=> array('TEXT_UNI', ''),
						'vote_likes'			=> array('USINT', 0),
						'vote_dislikes'			=> array('USINT', 0),						
					),
					'PRIMARY_KEY'	=> 'award_id',
				),

				$this->table_prefix . 'minty_sl_genetics'	=> array(
					'COLUMNS'		=> array(
						'seed_id'				=> array('UINT'),
						'genetic_id'		=> array('UINT'),
					),
					'PRIMARY_KEY'	=> 'seed_id, genetic_id',
				),


				$this->table_prefix . 'minty_sl_users_seeds'	=> array(
					'COLUMNS'		=> array(
						'user_id'				=> array('UINT'),
						'seed_id'				=> array('UINT'),
						'seed_count'			=> array('UINT'),
						'grown_yn'				=> array('BOOL', 0),
						'notes'			=> array('TEXT_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'user_id, seed_id',
				),
			),

			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_minty_seeds_enabled'	=> array('UINT', 1),
				),
			),
		);
	}

	/**
	 * Revert database schema changes. This method is almost always required
	 * to revert the changes made above by update_schema.
	 *
	 * https://area51.phpbb.com/docs/dev/3.2.x/migrations/schema_changes.html
	 *	add_tables: Add tables
	 *	drop_tables: Drop tables
	 *	add_columns: Add columns to a table
	 *	drop_columns: Removing/Dropping columns
	 *	change_columns: Column changes (only type, not name)
	 *	add_primary_keys: adding primary keys
	 *	add_unique_index: adding an unique index
	 *	add_index: adding an index (can be column:index_size if you need to provide size)
	 *	drop_keys: Dropping keys
	 *
	 * This sample migration removes the column that was added the users table in update_schema.
	 * It also removes the table that was added in update_schema.
	 *
	 * @return array Array of schema changes
	 */
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'user_minty_seeds_enabled',
				),
			),
			'drop_tables'		=> array(
				$this->table_prefix . 'minty_sl_seeds',				
				$this->table_prefix . 'minty_sl_breeder',
				$this->table_prefix . 'minty_sl_genetics',
				$this->table_prefix . 'minty_sl_users_seeds',
				$this->table_prefix . 'minty_sl_smells',
				$this->table_prefix . 'minty_sl_tastes',
				$this->table_prefix . 'minty_sl_effects',
				$this->table_prefix . 'minty_sl_meta_tags',
				$this->table_prefix . 'minty_sl_awards',
				$this->table_prefix . 'minty_sl_smell',
				$this->table_prefix . 'minty_sl_taste',
				$this->table_prefix . 'minty_sl_effect',
				$this->table_prefix . 'minty_sl_meta_tag',
				$this->table_prefix . 'minty_sl_award',
			),
		);
	}
}
