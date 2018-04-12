<?php

use Phalcon\Validation;

class EducationBackground extends Phalcon\Mvc\Model {

	public $id;
	public $teacher_id;
	public $primary_school;
	public $junior_high_school;
	public $senior_high_school;
    public $diploma;
    public $s1;
    public $s2;
	public $s3;

	public function validation()
	{
		$validator = new Validation();
		return $this->validate($validator);
	}

	public function initialize()
    {
        $this->setSchema("guru_les");
        $this->hasManyToMany(
            "teacher_id",
            "PrfTeacher",
            "id_teachers", "user_id",
            "RadiusLocations",
            "user_id"
        );
    }

	public function getSource()
    {
        return 'education_background';
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