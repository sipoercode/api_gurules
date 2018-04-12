<?php

class Schedules extends \Phalcon\Mvc\Model
{

    public $schedule_id;

    public $user_id;

    public $schedule_days;

    public $schedule_times;

    public function initialize()
    {
        $this->belongsTo('user_id', '\Users', 'id_user', ['alias' => 'Users']);
    }

    public function getSource()
    {
        return 'schedules';
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
