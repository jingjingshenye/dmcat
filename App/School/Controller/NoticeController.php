<?php

namespace App\School\Controller;


use Controller;
use Request;
use App\School\Model\UserModel;
use App\School\Model\StudentModel;
use App\School\Model\NoticeModel;
use App\School\Middleware\L;
use App\School\Tool\Func;
use App\School\Tool\AJAX;
use View;

class NoticeController extends Controller{


    private $L;


    function __construct(){

        $this->L = L::getInstance();
        

    }


    function get($id=0 ,NoticeModel $model){

        !$id && AJAX::success(['info'=>[]]);

        $info = $model->find($id);
        !$info && AJAX::error_i18n('no_data');

        AJAX::success(['info'=>$info]);

    }

    function lists(NoticeModel $model,$page = 1 ,$limit = 30){

        $out = ['get'=>'/notice/get','upd'=>'/notice/upd','del'=>'/notice/del'];

        $out['thead'] = [
            'ID'=>['class'=>'tc'],
            "标题/Title"=>['class'=>'tc'],
            "显示/Show"=>['class'=>'tc'],
            '_opt'=>['class'=>'tc'],
        ];
        
        $out['tbody'] = [
            'id'=>['class'=>'tc'],
            'title'=>['class'=>'tc'],
            'isshow'=>['class'=>'tc','type'=>'checkbox'],
            '_opt'=>['class'=>'tc'],
        ];


        $out['lang'] = $this->lang->language;

        $list = $model->page($page,$limit)->order('id','DESC')->get()->toArray();

        $out['list']  = $list;
        $out['max'] = $model->select('COUNT(*) as c','RAW')->find()->c;
        $out['page'] = $page;
        $out['limit'] = $limit;
        AJAX::success($out);
    }

    function del($id = 0,NoticeModel $model){

        !$this->L->id && AJAX::error_i18n('not_login');

        $model->remove($id);
        AJAX::success();

    }

    function upd($id = 0,NoticeModel $model){

        $data = Request::getInstance()->request($model->field);

        if($id){

            $info = $model->find($id);
            !$info && AJAX::error_i18n('no_data');

            !$model->set($data)->save($id)->getStatus() && AJAX::error_i18n('save_failed');

        }else{
            
            $data['create_time'] = TIME_NOW;

            unset($data['id']);
            
            $model->set($data)->add()->getStatus();

        }

        

        AJAX::success();
        
    }

    

}