<?php

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;

class Provinces extends \Phalcon\Mvc\Model
{
    
    public $id;
    public $name;

    public function validation()
    {
        $validator = new Validation();

        // $validator->add(
        //     'email',
        //     new EmailValidator(
        //         [
        //             'model'   => $this,
        //             'message' => 'Please enter a correct email address',
        //         ]
        //     )
        // );

        return $this->validate($validator);
    }

    public function initialize()
    {
        $this->setSchema("guru_les");
    }

    public function getSource()
    {
        return 'provinces';
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
