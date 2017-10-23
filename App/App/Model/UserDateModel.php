<?php

namespace App\App\Model;
use Model;

class UserDateModel extends Model{

    public $table = 'user_date';

    public function date(){

        return $this->join(DateModel::class,'id','date_id','LEFT');
    }

    public function doctor(){

        return $this->join(DoctorModel::class,'id','doctor_id');
    }
    public function user(){

        return $this->join(UserModel::class,'id','user_id');
    }
    public function clinic(){

        return $this->join(ClinicModel::class,'id','clinic_id');
    }
}