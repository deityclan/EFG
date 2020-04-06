<?php
namespace app\common\model;
use think\Model;    // 使用前进行声明

class Review extends Model{
    
    protected $dateFormat = 'Y年m月d日 H时i分s秒';    // 日期格式

    public function User(){
        return $this->belongsTo('User');
    }

    public function Admin(){
        return $this->belongsTo('Admin');
    }

    public function getReviewStageAttr($value){
    	switch($value){
    		case 0:
    		$value="初审";
    		break;
    		case 1:
    		$value="测试";
    		break;
    		default:
    		break;
    	}
    	return $value;
    }

    public function getReviewResultAttr($value){
    	switch($value){
    		case 1:
    		$value="通过";
    		break;
    		case 0:
    		$value="未通过";
    		break;
    		default:
    		break;
    	}
    	return $value;
    }
}
