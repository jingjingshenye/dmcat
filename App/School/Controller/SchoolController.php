<?php

namespace App\School\Controller;


use App\School\Model\SchoolModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;
use Model;
class SchoolController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 学校列表 */
    function lists(SchoolModel $model){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/school/get','upd'=>'/school/upd','del'=>'/school/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->school->school_name)=>['class'=>'tc'],
            ($this->lang->school->school_name_en)=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        $out['lang'] = $this->lang->language;

        $list = $model->get()->toArray();

        $out['list']  = $list;
        AJAX::success($out);


    }

    function get($id,SchoolModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

    function upd($id,SchoolModel $model){

        $data = Request::getInstance()->request(['name','name_en']);

        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }


    function del($id,SchoolModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }


    function add_notice($id){

        $this->L->check_type([5,6,7]);
        $data = Request::getInstance()->request(['title','short_message','content','isshow']);

        $model = Model::getInstance('notice');
        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        AJAX::success();

    }

    function add_school_message($message){

        $this->L->check_type([3,5]);

        if(!$message)AJAX::error_i18n('param_error');

        $data['type'] = 1;
        $data['user_id'] = $this->L->id;
        $data['message'] = $message;
        $data['create_time'] = TIME_NOW;

        Model::getInstance('school_message')->set($data)->add();

        AJAX::success();

    }


    


}