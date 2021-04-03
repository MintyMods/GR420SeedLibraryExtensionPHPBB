<?php

namespace minty\seeds\aps;

class update_seed extends \phpbbstudio\aps\actions\type\base {
	
	public function get_action() {
		return 'UPDATE_SEED_RECORD';
	}

	public function is_global()	{
		return true;
	}

	public function get_category() {
		return 'ACP_APS_MINTY_MODS_CATEGORY';
	}

	public function get_data() {
		return array(
			'minty_seeds_update_seed_record'		=> 'MINTY_SEEDS_UPDATE_SEED_RECORD',
		);
	}

	public function calculate($data, $values) {
		$values = $values[0];
		foreach (array_keys($this->users) as $user_id) {
			$this->add($user_id, array(
				'points'	=> $values['minty_seeds_update_seed_record'],
				'logs'		=> array(
					'MINTY_SEEDS_UPDATE_SEED_RECORD'	=> $values['minty_seeds_update_seed_record'],
				),
			));
		}
	}
}
