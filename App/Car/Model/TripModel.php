<?php

namespace App\Car\Model;
use Model;

class TripModel extends Model{

    public $table = 'trip';

    public function driver(){

        return $this->join(DriverModel::class,'id','driver_id');
    }

}