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
namespace app\admin\controller;
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
                    CONCAT(d.`name`,'-',e.`name`,'-',b.`code`,'号窗口') AS name_info,
                    CONCAT(a.`start_time`,'到',a.`end_time`) AS start_end,
                    b.`code` AS window_code,b.ip,c.advice_price,
                    c.`name` AS cook_menu_name FROM garnish a LEFT JOIN window b ON a.window_id = b.id LEFT JOIN cook_menu c ON a.cook_menu_id = c.id 
                    LEFT JOIN dinner d ON d.id = b.dinner_id LEFT JOIN floor e ON e.id = b.floor_id ";
    
    // 渲染页面
    public function index ()
    {
        return $this -> fetch();
    }

    // 为页面jqgrid分配数据
    public function jqgrid_all() 
    {
        $garnish = Db::name('garnish');
        $result = $garnish -> query($this -> sql);
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
    //添加
    public function add() 
    {
        return $this -> add_or_update(true);
    }

    //修改
    public function update() 
    {
        return $this -> add_or_update(false);
    }
    //添加或更新
    protected function add_or_update($is_add)
    {
        $id = '';
        $garnish = Db::name('garnish');
        // 判断是否为添加并且检测id是否合法
        if($is_add === false) {
            $id = $this -> request -> post('id');
            if(Common::check_id($garnish,$id) === true) {
                $data['id'] = $id;
            } else {
                return Common::check_id($garnish,$id);
            }
        }
        
        // 检测窗口是否合法
        $window_id = $this -> request -> post('window_id');
        
        if($this -> check_window_id($window_id) === true) {
            $data['window_id'] = $window_id;
        } else {
            return $this -> check_window_id($window_id);
        }
        
        // 检测菜单是否合法
        $cook_menu_id = $this -> request -> post('cook_menu_id');
        if($this -> check_cook_menu_id($cook_menu_id) === true) {
            $data['cook_menu_id'] = $cook_menu_id;
        } else {
            return $this -> check_cook_menu_id($cook_menu_id);
        }

        // 检测价格是否合法
        $price = $this -> request -> post('price');
        if(Common::check_empty($price) === false) {
            $data['price'] = $price;       
        } else {
            return Base::echo_error(Error::PRICE_IS_EMPTY);        
        }

        // 检测max_number是否合法
        $max_number = $this -> request -> post('max_number');
        if(Common::check_empty($max_number) === false) {
            $data['max_number'] = $max_number;       
        } else {
            return Base::echo_error(Error::MAX_NUMBER_IS_EMPTY);        
        }

        // 检测is_half是否合法,同时检测half_price是否合法
        $is_half = $this -> request -> post('is_half');
        $half_price = $this -> request -> post('half_price');
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
        $plan_number = $this -> request -> post('plan_number');
        if(Common::check_empty($plan_number) === false) {
            $data['plan_number'] = $plan_number;       
        } else {
            return Base::echo_error(Error::PLAN_NUMBER_IS_EMPTY);        
        }

        // 检测meals_time_id是否合法
        $meals_time_id = $this -> request -> post('meals_time_id');
        if(Common::check_empty($meals_time_id) === false) {
            $data['meals_time_id'] = $meals_time_id;       
        } else {
            return Base::echo_error(Error::MEALS_TIME_ID_IS_EMPTY);        
        }

        // 检测start_time是否合法
        $start_time = $this -> request -> post('start_time');
        if(Common::check_empty($start_time) === false) {
            $data['start_time'] = $start_time;       
        } else {
            return Base::echo_error(Error::START_TIME_IS_EMPTY);        
        }

        // 检测end_time是否合法
        $end_time = $this -> request -> post('end_time');
        if(Common::check_empty($end_time) === false) {
            $data['end_time'] = $end_time;       
        } else {
            return Base::echo_error(Error::END_TIME_IS_EMPTY);        
        }

        $sort = $this -> request -> post('sort');
        $data['sort'] = $sort;

        // 新增数据
        if ($is_add === true) {
            $data['id'] = Base::guid();
            if ($garnish -> insert($data) == 1) {
                return Base::echo_success(Error::ADD_SUCCESS);
            } else {
                return Base::echo_error(Error::ADD_ERROR);
            }
        }

        // 更新数据
        if ($is_add === false) {
            if ($garnish -> update($data) !== false) {
                return Base::echo_success(Error::UPDATE_SUCCESS);
            } else {
                return Base::echo_error(Error::UPDATE_ERROR);                
            }
        }
  
    }
    // 获取餐厅
    public function get_dinner ()
    {
        $dbname = Db::name('dinner');
        $row = $dbname -> select();
        if ($row === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return Base::echo_success($row);
    }
    // 获取楼层
    public function get_floor ()
    {
        $dbname = Db::name('floor');
        $row = $dbname -> select();
        if ($row === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return Base::echo_success($row);
    }

    // 删除
    public function delete () 
    {

        $ids = $this -> request -> post('ids');
        if (Common::check_empty($ids)) {
            Base::echo_error(Error::ARGUMENT_ERROR);
        }
        $ids = substr($ids,0,-1);
        $idArr = explode(',',$ids);

        // 启动事务
        Db::startTrans();
        $garnish = Db::name('garnish');
        for ($i = 0; $i < count($idArr) ; $i++) { 
            $res = $garnish -> where('id',$idArr[$i]) -> delete();
            
            if (!$res) {
                Db::rollback();
                return Base::echo_error(Error::DELETE_ERROR);
            }
        }
        // 提交事务            
        Db::commit();
        return Base::echo_success(Error::DELETE_SUCCESS);
        
    }
}
