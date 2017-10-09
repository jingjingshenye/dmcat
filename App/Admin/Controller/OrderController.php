<?php

namespace App\Admin\Controller;


use Controller;
use View;
use Request;
use App\Car\Middleware\L3;
use App\Car\Tool\Func;
use App\Car\Tool\AdminFunc;
use Uccu\DmcatTool\Tool\AJAX;

# 数据模型
use App\Car\Model\OrderDrivingModel;
use App\Car\Model\OrderTaxiModel;
use App\Car\Model\OrderWayModel;
use App\Car\Model\UserApplyModel;
use App\Car\Model\UserModel;


class OrderController extends Controller{

    function __construct(){

        $this->L = L3::getSingleInstance();
        $this->salt = $this->L->config->SITE_SALT;

    }


    /*  代驾 */
    function driving(){

        View::addData(['getList'=>'admin_driving']);
        View::hamlReader('home/list','Admin');
    }
    /* 出租车 */
    function taxi(){

        View::addData(['getList'=>'admin_taxi']);
        View::hamlReader('home/list','Admin');
    }
    /*  顺风车 */
    function way(){

        View::addData(['getList'=>'admin_way']);
        View::hamlReader('home/list','Admin');
    }
    function apply(){

        View::addData(['getList'=>'admin_apply']);
        View::hamlReader('home/list','Admin');
    }

    


    # 管理代驾订单
    function admin_driving(OrderDrivingModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(113);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_driving_get',
                'upd'   => '../order/admin_driving_upd',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '用户',
                '司机',
                '状态',
                '起点',
                '终点',
                '预估价(元)',
                

            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'user_name',
                'driver_name',
                'status_name',
                'start_name',
                'end_name',
                'estimated_price',


            ];
            

        # 列表内容
        $where = [];

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%n)','city_id',$this->L->userInfo->city_id];
        }
        
        if($search){
            $where['search'] = ['start_name LIKE %n OR end_name LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','user.name>user_name','driver.name>driver_name')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = ['取消','待接单','接客种','服务中','待付款','待评价','已完成'][$v->status];
        }


        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_driving_get(OrderDrivingModel $model,$id){

        $this->L->adminPermissionCheck(113);
        $name = '';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_driving_get',
                'back'  => 'order/driving',
                'view'  => 'home/upd',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '开始维度',
                    'name'  =>  'start_latitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '开始经度',
                    'name'  =>  'start_longitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '开始地址名字',
                    'name'  =>  'start_name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束维度',
                    'name'  =>  'end_latitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束经度',
                    'name'  =>  'end_longitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束地址名字',
                    'name'  =>  'end_name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],

                
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }
    


    # 管理出租车
    function admin_taxi(OrderTaxiModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(114);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_taxi_get',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '用户',
                '司机',
                '状态',
                '起点',
                '终点',
                '预估价(元)',
                '打表'
                

            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'user_name',
                'driver_name',
                'status_name',
                'start_name',
                'end_name',
                'estimated_price',
                [
                    'name'=>'meter',
                    'type'=>'checkbox',
                    'disabled'=>true
                ]

            ];
            

        # 列表内容
        $where = [];

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%n)','city_id',$this->L->userInfo->city_id];
        }
        
        if($search){
            $where['search'] = ['start_name LIKE %n OR end_name LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','user.name>user_name','driver.name>driver_name')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = ['取消','待接单','接客种','服务中','待付款','待评价','已完成'][$v->status];
        }


        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_taxi_get(OrderTaxiModel $model,$id){

        $this->L->adminPermissionCheck(114);
        $name = '';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_taxi_get',
                'back'  => 'order/taxi',
                'view'  => 'home/upd',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '开始维度',
                    'name'  =>  'start_latitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '开始经度',
                    'name'  =>  'start_longitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '开始地址名字',
                    'name'  =>  'start_name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束维度',
                    'name'  =>  'end_latitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束经度',
                    'name'  =>  'end_longitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束地址名字',
                    'name'  =>  'end_name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],

                
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }


    # 管理顺风车
    function admin_way(OrderWayModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(115);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_way_get',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                'ID',
                '用户',
                '司机',
                '状态',
                '起点',
                '终点',
                '预估价(元)',
                

            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'user_name',
                'driver_name',
                'status_name',
                'start_name',
                'end_name',
                'estimated_price',

            ];
            

        # 列表内容
        $where = [];

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%n)','city_id',$this->L->userInfo->city_id];
        }
        
        if($search){
            $where['search'] = ['start_name LIKE %n OR end_name LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','user.name>user_name','driver.name>driver_name')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = ['取消','待接单','接客种','服务中','待付款','待评价','已完成'][$v->status];
        }


        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    function admin_way_get(OrderWayModel $model,$id){

        $this->L->adminPermissionCheck(115);
        $name = '';

        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_way_get',
                'back'  => 'order/way',
                'view'  => 'home/upd',

            ];
        $tbody = 
            [
                [
                    'type'  =>  'hidden',
                    'name'  =>  'id',
                ],
                [
                    'title' =>  '开始维度',
                    'name'  =>  'start_latitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '开始经度',
                    'name'  =>  'start_longitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '开始地址名字',
                    'name'  =>  'start_name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束维度',
                    'name'  =>  'end_latitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束经度',
                    'name'  =>  'end_longitude',
                    'size'  =>  '4',
                    'disabled'=>true
                ],
                [
                    'title' =>  '结束地址名字',
                    'name'  =>  'end_name',
                    'size'  =>  '4',
                    'disabled'=>true
                ],

                
                

            ];

        !$model->field && AJAX::error('字段没有公有化！');


        $info = AdminFunc::get($model,$id);

        if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;

        $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];

        AJAX::success($out);

    }


    # 顺风车申请
    function admin_apply(UserApplyModel $model,$page = 1,$limit = 10,$search){
        
        $this->L->adminPermissionCheck(116);

        $name = '';
        # 允许操作接口
        $opt = 
            [
                'get'   => '../order/admin_apply_get',
                'view'  => 'home/upd',
                'req'   =>[
                    [
                        'title'=>'搜索',
                        'name'=>'search',
                        'size'=>'3'
                    ],
                ]
            ];

        # 头部标题设置
        $thead = 
            [

                '用户ID',
                '名字',
                '品牌',
                '车牌',
                '驾照',
                '行驶证',
                '城市',
                '状态',
                '申请时间'
                
            ];


        # 列表体设置
        $tbody = 
            [

                
                'id',
                'name',
                'brand',
                'car_number',
                [
                    'type'=>'pic',
                    'name'=>'driving_license',
                    'href'=>true
                ],
                [
                    'type'=>'pic',
                    'name'=>'driving_permit',
                    'href'=>true
                ],
                'city',
                'status_name',
                'date',

            ];
            

        # 列表内容
        $where = [];

        if($this->L->userInfo->type == 2){
            $where['city.parent_id'] = $this->L->userInfo->province_id;
        }elseif($this->L->userInfo->type == 1){
            $where['city_id'] = ['%F IN (%n)','city_id',$this->L->userInfo->city_id];
        }
        
        if($search){
            $where['search'] = ['user.name LIKE %n OR user.phone LIKE %n','%'.$search.'%','%'.$search.'%'];
        }

        $list = $model->select('*','user.name','city.areaName>city')->order('create_time desc')->where($where)->page($page,$limit)->get()->toArray();
        foreach($list as &$v){
            $v->status_name = [
                '0'=>'申请中',
                '1'=>'审核通过',
                '-1'=>'审核失败'
            ][$v->status];
            $v->driving_license = Func::fullPicAddr($v->driving_license);
            $v->driving_permit = Func::fullPicAddr($v->driving_permit);
            $v->date = date('Y-m-d H:i');
        }


        # 分页内容
        $page   = $page;
        $max    = $model->where($where)->select('COUNT(*) AS c','RAW')->find()->c;
        $limit  = $limit;

        # 输出内容
        $out = 
            [

                'opt'   =>  $opt,
                'thead' =>  $thead,
                'tbody' =>  $tbody,
                'list'  =>  $list,
                'page'  =>  $page,
                'limit' =>  $limit,
                'max'   =>  $max,
                'name'  =>  $name,
            
            ];

        AJAX::success($out);

    }
    
    function admin_apply_get(UserApplyModel $model,$id){
        
        $this->L->adminPermissionCheck(116);
        $name = '';
        
        # 允许操作接口
        $opt = 
        [
            'get'   => '../order/admin_apply_get',
            'upd'   => '../order/admin_apply_upd',
            'back'  => 'order/apply',
            'view'  => 'home/upd',
            
        ];
        $tbody = 
        [
            [
                'type'  =>  'hidden',
                'name'  =>  'id',
            ],
            [
                'title' =>  '状态',
                'name'  =>  'status',
                'type'  =>  'select',
                'option'=>[
                    '0'=>'申请中',
                    '1'=>'审核通过',
                    '-1'=>'审核失败'
                    ]
                ],
                
                
                
                
            ];
            
            !$model->field && AJAX::error('字段没有公有化！');
            
            
            $info = AdminFunc::get($model,$id);
            
            if(!in_array($info->master_type,[0,1,2]))$info->master_type = -1;
            
            $out = 
            [
                'info'  =>  $info,
                'tbody' =>  $tbody,
                'name'  =>  $name,
                'opt'   =>  $opt,
            ];
            
            AJAX::success($out);
            
        }
        
        



    function admin_apply_upd(UserApplyModel $model,$id,$status){
        $this->L->adminPermissionCheck(116);
        !$model->field && AJAX::error('字段没有公有化！');
        $data = Request::getSingleInstance()->request($model->field);
        
        $app = $model->find($id);
        !$app && AJAX::error('error');

        if($status == 1){
            
            UserModel::copyMutiInstance()->set(['type'=>1,'city_id'=>$app->city_id])->save($id);
        }else{
            UserModel::copyMutiInstance()->set(['type'=>0,'city_id'=>$app->city_id])->save($id);
        }

        $upd = AdminFunc::upd($model,$id,$data);
        $out['upd'] = $upd;
        AJAX::success($out);
    }
    
}
    