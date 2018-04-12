<?php

class PrfStudents extends \Phalcon\Mvc\Model
{

    public $id_students;

    public $user_id;

    public $full_name;

    public $birthday;
    
    public $gender;
    
    public $descriptions;

    public $address;

    public $city;

    public $provinces;

    public $districts;

    public $villages;

 
    public function getSource()
    {
        return "prf_students";
    }
 
    public function initialize()
    {
        $this->belongsTo("user_id", "Users", "id_user");
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
