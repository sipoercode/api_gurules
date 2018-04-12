<?php

use Phalcon\Mvc\Model;

use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Regex;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Subjects extends Model {

	// Columns
	public $id_subject;
	public $subject;
	public $sub_subject;

	// Validations
	public function validation() {

		// name is required
		$this->validate(
			new PresenceOf(
				array(
					"field"   => "subject",
					"message" => "The name is required.",
				)
			)
		);

		// // engine number is required
		// $this->validate(
		// 	new PresenceOf(
		// 		array(
		// 			"field"   => "engine_no",
		// 			"message" => "The engine number is required.",
		// 		)
		// 	)
		// );

		// // Owner's name is required
		// $this->validate(
		// 	new PresenceOf(
		// 		array(
		// 			"field"   => "owner_name",
		// 			"message" => "The owner name is required.",
		// 		)
		// 	)
		// );

		// name uniqueness check
		$this->validate(
			new Uniqueness(
				array(
					"field"   => "subject",
					"message" => "The name is already used.",
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
					"field"   => "name",
					"pattern" => "/^[A-Z]{3}-[0-9]{3}$/",
					"message" => "Invalid name.",
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