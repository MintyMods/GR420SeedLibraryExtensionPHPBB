<?php
/**
 *
 * Minty Seed Library. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Minty, https://www.mintymods.info/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace minty\seeds;

class service {

	protected $user;
	protected $table_name;

	public function __construct(\phpbb\user $user, $table_name) {
		$this->user = $user;
		$this->table_name = $table_name;
	}

	public function get_user() {
		var_dump($this->table_name);
		return $this->user;
	}
}
