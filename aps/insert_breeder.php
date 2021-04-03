<?php

namespace minty\seeds\aps;

class insert_breeder extends \phpbbstudio\aps\actions\type\base {

	public function get_action() {
		return 'INSERT_BREEDER_RECORD';
	}

	public function is_global() {
		return true;
	}

	public function get_category() {
		return 'ACP_APS_MINTY_MODS_CATEGORY';
	}

	public function get_data() {
		return array(
			'minty_seeds_insert_breeder_record'		=> 'MINTY_SEEDS_INSERT_BREEDER_RECORD',
		);
	}

	public function calculate($data, $values) {
		$values = $values[0];
		$breeder_name = 'Breeder Name : bob the breeder';
		foreach (array_keys($this->users) as $user_id) {
			$this->add($user_id, array(
				'points'	=> $values['minty_seeds_insert_breeder_record'],
				'logs'		=> array(
					'MINTY_SEEDS_INSERT_BREEDER_RECORD'	=>  $values['minty_seeds_insert_breeder_record'],
					$breeder_name =>  0,
				),
			));
		}
	}
}
