<?php

namespace App\Car\Model;
use Model;

class OrderWayModel extends Model{

    public $table = 'order_way';

    public function user(){

        return $this->join(UserModel::class,'id','user_id');
    }
    public function driver(){

        return $this->join(UserModel::class,'id','driver_id','LEFT');
    }
    public function city(){
        
        return $this->join(AreaModel::class,'id','city_id');
    }
    public function stat(){
        
        return $this->join(StatusModel::class,'id','statuss','LEFT');
    }

    public function trip(){
        
        return $this->join(TripModel::class,'id','id');
    }
}