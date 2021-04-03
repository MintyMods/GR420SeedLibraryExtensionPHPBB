<?php

namespace minty\seeds\aps;

class delete_seed extends \phpbbstudio\aps\actions\type\base {

	public function get_action() {
		return 'DELETE_SEED_RECORD';
	}

	public function is_global() {
		return true;
	}

	public function get_category() {
		return 'ACP_APS_MINTY_MODS_CATEGORY';
	}

	public function get_data() {
		return array(
			'minty_seeds_delete_seed_record'		=> 'MINTY_SEEDS_DELETE_SEED_RECORD',
		);
	}

	public function calculate($data, $values) {
		$values = $values[0];
		foreach (array_keys($this->users) as $user_id) {
			$this->add($user_id, array(
				'points'	=> $values['minty_seeds_delete_seed_record'],
				'logs'		=> array(
					'MINTY_SEEDS_DELETE_SEED_RECORD'	=> $values['minty_seeds_delete_seed_record'],
				),
			));
		}
	}
}
