<?php

use Phalcon\Validation;

class LearningReport extends Phalcon\Mvc\Model {

	public $id_report;
	public $id_ticket;
	public $feed_back;

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
        return 'learning_report';
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