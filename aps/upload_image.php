<?php

namespace minty\seeds\aps;

class upload_image extends \phpbbstudio\aps\actions\type\base {

	public function get_action() {
		return 'UPLOAD_IMAGE';
	}

	public function is_global() {
		return true;
	}

	public function get_category() {
		return 'ACP_APS_MINTY_MODS_CATEGORY';
	}

	public function get_data() {
		return array(
			'minty_seeds_upload_image'		=> 'MINTY_SEEDS_UPLOAD_IMAGE',
		);
	}

	public function calculate($data, $values) {
		$values = $values[0];
		foreach (array_keys($this->users) as $user_id) {
			$this->add($user_id, array(
				'points'	=> $values['minty_seeds_upload_image'],
				'logs'		=> array(
					'MINTY_SEEDS_UPLOAD_IMAGE'	=> $values['minty_seeds_upload_image'],
				),
			));
		}
	}
}
