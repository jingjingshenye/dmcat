<?php

namespace App\Resource\Model;
use Model;

class ThemeModel extends Model{

    public $table = 'theme';

    

    

    function matchSearch($match){

        return $this->where('MATCH(%F)AGAINST(%n)','matches',$match)->find();

    }
    

    
}