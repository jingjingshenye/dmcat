<?php

namespace App\Resource\Model;
use Model;

class ThemeModel extends Model{

    public $table = 'theme';

    protected $field = ['id','name','new_name','content','last_number',
    'change_time','tags','matches','visible','level','ctime'];

    

    function updateMatches(){



    }
    

    
}