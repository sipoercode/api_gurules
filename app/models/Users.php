<?php

use Phalcon\Validation;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;

class Users extends \Phalcon\Mvc\Model
{
    
    public $id_user;
    public $uid;
    public $email;
    public $username;
    public $password;
    public $account_type;
    public $created_at;
    public $created_by;
    public $updated_at;
    public $updated_by;

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
        $this->hasMany('id_user', 'PrfTeachers', 'user_id');
        $this->hasMany('id_user', 'Schedules', 'user_id', ['alias' => 'Schedules']);
    }

    public function getSource()
    {
        return 'users';
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
