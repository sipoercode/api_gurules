<?php

use Phalcon\Validation;

class Inbox extends Phalcon\Mvc\Model {

	public $id_inbox;
    public $user_id;
	public $title_type;
	public $content;
    public $is_read;
	public $status;
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
        return 'inbox';
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