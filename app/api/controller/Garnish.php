<?php
// +----------------------------------------------------------------------
// | @projectName  【order_system---点餐系统】
// +----------------------------------------------------------------------
// | @author        山西创客空间科技有限公司
// +----------------------------------------------------------------------
// | @date          2017年10月13日 星期五
// +----------------------------------------------------------------------
// | @Copyright     http://sx-ck.com All rights reserved.      
// +----------------------------------------------------------------------
namespace app\api\controller;

use app\admin\model\Common;
use app\common\Base;
use app\common\Error;
use think\Controller;
use think\Db;
use think\Request;

class Garnish extends Base 
{

    private $sql = " SELECT a.*,CASE a.is_half WHEN 0 THEN '否' ELSE '是' END AS is_half_info, 
                    CASE a.meals_time_id WHEN 0 THEN '早餐' WHEN 1 THEN '午餐' WHEN 2 THEN '晚餐' ELSE '' END AS meals_time_info,
                    CONCAT(d.`name`,'-',e.`name`,'-',b.`code`,'号窗口') AS name_info,CONCAT(a.`start_time`,'到',a.`end_time`) AS start_end,
                    b.`code` AS window_code,b.ip,c.advice_price,c.`name` AS cook_menu_name,CONCAT(\"/static/upload/cook_menu/\",c.img_url) AS img_url FROM garnish a LEFT JOIN window b ON a.window_id = b.id 
                    LEFT JOIN cook_menu c ON a.cook_menu_id = c.id LEFT JOIN dinner d ON d.id = b.dinner_id LEFT JOIN floor e ON e.id = b.floor_id "; 

    // 菜品展示接口
    public function show_garnish() 
    {
        $garnish = Db::name('garnish');
        $window_id = $this -> request -> post('window_id');
        if (Common::check_empty($window_id) === true) {
            return Base::echo_error(Error::ID_IS_EMPTY);
        }

        $date = $this -> request -> post('date');
        if (Common::check_empty($date) === true) {
            return Base::echo_error(Error::DATE_IS_EMPTY);
        }
        
        $del_sql = " SELECT a.* FROM garnish a WHERE a.adjust_time IS NOT NULL AND a.adjust_time != '$date' ";
        $del_row = $garnish -> query($del_sql);
        foreach ($del_row as $key => $value) {
            $garnish -> where('id','=',"{$value['id']}") -> delete();
        }
        $result = $garnish -> query($this -> sql."WHERE b.id = '$window_id' AND '$date' >= a.start_time AND '$date' <= a.end_time");
        
        if ($result === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return $this -> echo_success ($result);
    }
    
     // 检测窗口id
     protected function check_window_id ($window_id) 
     {
         $window = Db::name('window');
         if(Common::check_empty($window_id)) {
             return Base::echo_error(Error::WINDOW_ID_IS_EMPTY);
         }
         $row = $window -> where("id",'=',"$window_id") -> select();
         if($row === false) {
             return Base::echo_error(Error::DB_ERROR);
         }
         if(count($row) === 0) {
             return Base::echo_error(Error::WINDOW_ID_NOT_EXIST);
         }
         return true;
     }
     // 检测菜单id
     protected function check_cook_menu_id ($cook_menu_id) 
     {
         $dbname = Db::name('cook_menu');
         if(Common::check_empty($cook_menu_id)) {
             return Base::echo_error(Error::COOK_MENU_ID_IS_EMPTY);
         }
         $row = $dbname -> where("id",'=',"$cook_menu_id") -> select();
         if($row === false) {
             return Base::echo_error(Error::DB_ERROR);
         }
         if(count($row) === 0) {
             return Base::echo_error(Error::COOK_MENU_ID_NOT_EXIST);
         }
         return true;
     }

     public function test () {
        
        // $json = {"window_id":"1","cook_menu_id":"2642B811-C96B-4AE1-8DEF-1C282D73BC58","price":1,"max_number":1,"is_half":0,"meals_time_id":1,"plan_number":1,"start_time":"2017-10-24","end_time":"2017-10-31","sort":1,"half_price":0,"adjust_time":"2017-10-25"};
     }
    // 调菜的接口
    public function adjust_garnish () 
    {   
        // 接收post参数
        $garnish_json = $this -> request -> post('garnish_json');
        
        // 检测参数是否合法
        if (Common::check_empty($garnish_json) === true) {
            return Base::echo_error(Error::ARGUMENT_IS_EMPTY);
        }
        $garnish_arr = json_decode ($garnish_json,true);
        $data = [];

        // 检测窗口是否合法
        $window_id = $garnish_arr['window_id'];
        if($this -> check_window_id($window_id) === true) {
            $data['window_id'] = $window_id;
        } else {
            return $this -> check_window_id($window_id);
        }

        // 检测菜单是否合法
        $cook_menu_id = $garnish_arr['cook_menu_id'];
        if($this -> check_cook_menu_id($cook_menu_id) === true) {
            $data['cook_menu_id'] = $cook_menu_id;
        } else {
            return $this -> check_cook_menu_id($cook_menu_id);
        }
        
        // 检测价格是否合法
        $price = $garnish_arr['price'];
        if(Common::check_empty($price) === false) {
            $data['price'] = $price;       
        } else {
            return Base::echo_error(Error::PRICE_IS_EMPTY);        
        }
        // 检测max_number是否合法
        $max_number = $garnish_arr['max_number'];
        if(Common::check_empty($max_number) === false) {
            $data['max_number'] = $max_number;       
        } else {
            return Base::echo_error(Error::MAX_NUMBER_IS_EMPTY);        
        }
        
        // 检测is_half是否合法,同时检测half_price是否合法
        $is_half = $garnish_arr['is_half'];
        $half_price = $garnish_arr['half_price'];
        if(Common::check_empty($is_half) === false) {
            $data['is_half'] = $is_half;  
            if ($is_half == 1) {
                if (Common::check_empty($half_price) === true) {
                    return Base::echo_error(Error::HALF_PRICE_IS_EMPTY);
                } else {
                    $data['half_price'] = $half_price;
                }   
            } else {
                $data['half_price'] = $half_price;
            }
        } else {
            return Base::echo_error(Error::IS_HALF_IS_EMPTY);        
        }
        
        // 检测plan_number是否合法
        $plan_number = $garnish_arr['plan_number'];
        if(Common::check_empty($plan_number) === false) {
            $data['plan_number'] = $plan_number;       
        } else {
            return Base::echo_error(Error::PLAN_NUMBER_IS_EMPTY);        
        }

        // 检测meals_time_id是否合法
        $meals_time_id = $garnish_arr['meals_time_id'];
        if(Common::check_empty($meals_time_id) === false) {
            $data['meals_time_id'] = $meals_time_id;       
        } else {
            return Base::echo_error(Error::MEALS_TIME_ID_IS_EMPTY);        
        }

        // 检测start_time是否合法
        $start_time = $garnish_arr['start_time'];
        if(Common::check_empty($start_time) === false) {
            $data['start_time'] = $start_time;       
        } else {
            return Base::echo_error(Error::START_TIME_IS_EMPTY);        
        }

        // 检测end_time是否合法
        $end_time = $garnish_arr['end_time'];
        if(Common::check_empty($end_time) === false) {
            $data['end_time'] = $end_time;       
        } else {
            return Base::echo_error(Error::END_TIME_IS_EMPTY);        
        }

        // 检测adjust_time是否合法
        $adjust_time = $garnish_arr['adjust_time'];
        if(Common::check_empty($adjust_time) === false) {
            $data['adjust_time'] = $adjust_time;       
        } else {
            return Base::echo_error(Error::ADJUST_TIME_IS_EMPTY);        
        }

        $sort = $garnish_arr['sort'];
        $data['sort'] = $sort;

        $garnish = Db::name('garnish');
        $sql = " SELECT a.* FROM garnish a WHERE a.window_id = '$window_id' AND a.cook_menu_id = '$cook_menu_id' 
                AND a.start_time = '$start_time' AND a.end_time = '$end_time' ";
        
        $row = $garnish -> query($sql); 
        if ($row === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        if (count($row) > 0) {
            return Base::echo_error(Error::COOK_MENU_IS_EXIST);
        }

        $data['id'] = Base::guid();
        if ($garnish -> insert($data) == 1) {
            return Base::echo_success(Error::ADD_SUCCESS);
        } else {
            return Base::echo_error(Error::ADD_ERROR);
        }

        
    }





}

