<?php

class Rating extends \Phalcon\Mvc\Model
{

    public $id;

    public $teacher_id;

    public $student_id;

    public $rating;
    
    public $rating_date;

    public $rating_comments;

 
    public function getSource()
    {
        return "rating";
    }
 
    public function initialize()
    {
        $this->belongsTo("teacher_id", "PrfTeachers", "id_teachers");
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
