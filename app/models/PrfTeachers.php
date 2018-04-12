<?php

class PrfTeachers extends \Phalcon\Mvc\Model
{

    public $id_teachers;

    public $user_id;

    public $full_name;

    public $birthday;
    
    public $gender;

    public $current_job;
    
    public $descriptions;

    public $address;

    public $city;

    public $provinces;

    public $districts;

    public $villages;

 
    public function getSource()
    {
        return "prf_teachers";
    }
 
    public function initialize()
    {
        $this->belongsTo("user_id", "Users", "id_user");
        $this->belongsTo("id_teachers", "EducationBackground", "teacher_id");
        $this->belongsTo("user_id", "RadiusLocations", "user_id");
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
