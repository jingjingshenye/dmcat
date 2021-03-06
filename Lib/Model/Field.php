<?php

namespace Lib\Model;
use Uccu\DmcatTool\Tool\E;

use Lib\Model\Using;

class Field{


    public $table = '';
    
    public $fullTable = '';

    public $name = '';

    public $fullName = '';

    function __construct($field,$model,$checkField = true){
        
        //必须字符串类型
        if(!is_string($field))E::throwEx('Undefined Field\'s Name',1);

        //加载工具库
        $this->tool = Using::getSingleInstance();

        
        $fields = explode('.',$field);

        $count = count($fields);
        
        if($count!=1){
            
            for($i=0;$i<$count-1;$i++){

                $field = $fields[$i];
                $model = $model->$field;
                if(!$model)E::throwEx('Table Link `'.$field.'` Not Defined',2);
            }

        }
        $field = end($fields);
        $this->model = $model;
        $this->fullTable = $model->asRawTable && $model->asRawTable!=$model->table ? $model->table.' '.$model->asRawTable : $model->table;
        $this->table = $model->asRawTable ? $model->asRawTable : $model->table;
        if($checkField && $field != '*' && !$model->hasField($field))E::throwEx('Field `'.$field.'` Not Defined',2);

        
        
        $this->name = $field == '*' ? $field :$this->tool->quote_field( $field );

        if($checkField){
            $rawName = $checkField ? $model->getKeyField($field) :false;

            if($rawName && !is_numeric($rawName)){

                $this->fullName = $this->tool->format($rawName,array(),$model);
                $this->asName = $this->fullName.' AS '.$this->name;

            }else{

                $this->fullName = $this->table.'.'.$this->name;
                $this->asName = $this->fullName;
            }

            

        }else{

            $this->fullName = $this->table.'.'.$this->name;
            $this->asName = $this->fullName;
        }
        


        


    }







}