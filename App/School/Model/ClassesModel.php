<?php

namespace App\School\Model;
use Model;

class ClassesModel extends Model{

    public $table = 'classes';


    public function school(){

        return $this->join('App\School\Model\SchoolModel','id','school_id');
        
    }
    
    public function level(){

        return $this->join('App\School\Model\ClassesLevelModel','id','level');
        
    }

    public function user(){

        return $this->join('App\School\Model\UserClassesModel','classes_id','id');
        
    }
    

}