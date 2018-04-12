<?php

use Phalcon\Validation;

class LearningTicket extends Phalcon\Mvc\Model {

	public $id_ticket;
	public $id_transaction;
	public $ticket_no;
	public $created_at;
	public $created_by;

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
        return 'learning_ticket';
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