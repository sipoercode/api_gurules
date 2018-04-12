<?php

class RadiusLocations extends \Phalcon\Mvc\Model
{

    public $radius_id;

    public $user_id;

    public $city;

    public $district;
    
 
    public function getSource()
    {
        return "radius_locations";
    }
 
    public function initialize()
    {
        $this->belongsTo("user_id", "Users", "id_user");
        $this->hasMany("user_id", "PrfTeachers", "user_id");
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
