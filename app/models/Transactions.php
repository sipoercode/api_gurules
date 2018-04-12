<?php

use Phalcon\Validation;

class Transactions extends Phalcon\Mvc\Model {

	public $id_transaction;
	public $id_prf_teacher;
	public $id_prf_student;
	public $dates;
	public $student_name;
	public $lession_name;
	public $times;
	public $status;
	public $packages_type;
	public $address_location;
	public $price_session;
	public $total_session;
	public $is_accept;

	public function validation()
	{
		$validator = new Validation();
		return $this->validate($validator);
	}

	public function initialize()
    {
        $this->setSchema("guru_les");
    }

	public function getSource()
    {
        return 'transactions';
    }

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}