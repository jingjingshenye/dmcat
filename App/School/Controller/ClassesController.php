<?php

namespace App\School\Controller;


use App\School\Model\ClassesModel;
use Controller;
use Request;
use App\School\Tool\AJAX;
use App\School\Middleware\L;

class ClassesController extends Controller{


    function __construct(){

        $this->L = L::getInstance();
        $this->lang = $this->L->i18n;

    }


    /* 班级列表 */
    function lists(ClassesModel $model,$school_id){

        // !$this->L->id && AJAX::error_i18n('not_login');

        $out = ['get'=>'/classes/get','upd'=>'/classes/upd','del'=>'/classes/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            ($this->lang->classes->class_name)=>['class'=>'tc'],
            ($this->lang->classes->class_name_en)=>['class'=>'tc'],
            ($this->lang->school->school_name)=>['class'=>'tc'],
            
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'name'=>['class'=>'tc'],
            'name_en'=>['class'=>'tc'],
            'school_name'=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];

        $schoolName = $this->lang->language == 'cn' ? 'school.name>school_name' : 'school.name_en>school_name';
        
        $out['lang'] = $this->lang->language;

        if($school_id)$model->where(['school_id'=>$school_id]);

        $list = $model->select('*', $schoolName )->get()->toArray();

        $out['list']  = $list;
        AJAX::success($out);


    }

    function get($id,ClassesModel $model){

        !$id && AJAX::success(['info'=>[]]);
        $out['info'] = $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');


        AJAX::success($out);

    }

    function upd($id,ClassesModel $model){

        $data = Request::getInstance()->request(['name','name_en','school_id']);

        if(!$id){
            
            $data['create_time'] = TIME_NOW;
            $model->set($data)->add();

        }else{
            $model->set($data)->save($id);
        }
        
        

        AJAX::success();

    }


    function del($id,ClassesModel $model){

        !$id && AJAX::error_i18n('param_error');
        $model->remove($id);
        AJAX::success();

    }


    


}