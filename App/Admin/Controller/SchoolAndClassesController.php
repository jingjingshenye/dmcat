<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\School\Middleware\L;

class SchoolAndClassesController extends Controller{

    function __construct(){

        $this->L = L::getInstance();
        

    }

    /* 学校 */
    function school(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->school;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');
    }
    
    /* 班级 */
    function classes(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->classes;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    /* 班级 */
    function level(){

        $lang = $this->L->i18n;
        $lang->adminIndex;
        $lang->level;
        View::addData(['lang'=>$lang]);
        View::hamlReader(Request::getInstance()->folder[1].'/'.__FUNCTION__,'Admin');

    }

    


}