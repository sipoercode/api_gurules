<?php

use Phalcon\Mvc\Model;

use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Regex;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Villages extends Model {

	// Columns
	public $id;
	public $district_id;
	public $name;

	// Validations
	public function validation() {

		// province_id is required
		$this->validate(
			new PresenceOf(
				array(
					"field"   => "district_id",
					"message" => "The district_id is required.",
				)
			)
		);

		// name is required
		$this->validate(
			new PresenceOf(
				array(
					"field"   => "name",
					"message" => "The name is required.",
				)
			)
		);

		// province_id uniqueness check
		$this->validate(
			new Uniqueness(
				array(
					"field"   => "district_id",
					"message" => "The district_id is already used.",
				)
			)
		);

		// name is required
		$this->validate(
			new Uniqueness(
				array(
					"field"   => "name",
					"message" => "The name is required.",
				)
			)
		);

		// engine number's uniqueness check
		// $this->validate(
		// 	new Uniqueness(
		// 		array(
		// 			"field"   => "engine_no",
		// 			"message" => "The engine number is already used.",
		// 		)
		// 	)
		// );

		// Regular Expression to verify name pattern
		$this->validate(
			new Regex(
				array(
					"field"   => "district_id",
					"pattern" => "/^[0-9]{3}$/",
					"message" => "Invalid id district_id.",
				)
			)
		);

		// Custom Validation
		// if ($this->car_model_year < 0) {
		// 	$this->appendMessage(new Message("Car's model year can not be zero."));
		// }

		if ($this->validationHasFailed() == true) {
			return false;
		}
	}
}