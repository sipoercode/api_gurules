<?php

use Phalcon\Mvc\Model;

use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Regex;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class Districts extends Model {

	// Columns
	public $id;
	public $regency_id;
	public $name;

	// public function getSource() {
	// 	return 'ref_user';
	// }

	// /**
	//  * Allows to query a set of records that match the specified conditions
	//  *
	//  * @param mixed $parameters
	//  * @return RefUser[]
	//  */
	// public static function find($parameters = null) {
	// 	return parent::find($parameters);
	// }

	// /**
	//  * Allows to query the first record that match the specified conditions
	//  *
	//  * @param mixed $parameters
	//  * @return RefUser
	//  */
	// public static function findFirst($parameters = null) {
	// 	return parent::findFirst($parameters);
	// }

	// Validations
	public function validation() {

		// regency_id is required
		$this->validate(
			new PresenceOf(
				array(
					"field"   => "regency_id",
					"message" => "The regency_id is required.",
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

		// regency_id uniqueness check
		$this->validate(
			new Uniqueness(
				array(
					"field"   => "regency_id",
					"message" => "The regency_id is already used.",
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
					"field"   => "regency_id",
					"pattern" => "/^[0-9]{3}$/",
					"message" => "Invalid id regency_id.",
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