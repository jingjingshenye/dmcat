<?php

namespace App\Admin\Controller;

use Controller;
use View;
use App\School\Middleware\L;


class LoginController extends Controller{


    function __construct(){

        $this->L = L::getInstance();

        $array = [1];

        if($this->L->id && $this->L->userInfo->type){
            header('Location:/admin/index');
        }

        $lang = $this->L->i18n;

        $lang->adminLogin;

        View::addData(['lang'=>$lang]);

        View::hamlReader('login','Admin');
    }



    


}