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

class Allorder extends Base 
{
    public function index () {
        return $this -> fetch ();
    }

    // 查看订单
    public function show_orders ($query = [],$window_id = '') 
    {
        // 连接mongodb数据库
        $dbname = 'order_system_'.date('Y');
        $db = Base::connect_mongodb($dbname);
        $row = [];

        if (Common::check_empty($window_id) === true) {
            // 查询所有窗口
            $window = Db::name('window');
            $windowRow = $window  -> field('id') -> select();
            if ($windowRow === false) {
                return Base::echo_error(Error::DB_ERROR);
            }
            foreach ($windowRow as $windowkey => $windowVal) {
                // 连接表
                $collection = $db -> selectCollection(md5($windowVal['id']));
                $cursor = $collection -> find($query);
                if ($cursor === false) {
                    return Base::echo_error(Error::DB_ERROR);
                }
                foreach ($cursor as $doc) {
                    $row[] = $doc;
                }
            }
        } else {
            $collection = $db -> selectCollection(md5($window_id));
            $cursor = $collection -> find($query);
            if ($cursor === false) {
                return Base::echo_error(Error::DB_ERROR);
            }
            foreach ($cursor as $doc) {
                $row[] = $doc;
            }
        }
        
        return Base::echo_success($row);
    }

    // 查看订单明细
    public function get_order_info () 
    {
        $order_id = $this -> request -> post('order_id');
        if (Common::check_empty($order_id) === true) {
            return Base::echo_error(Error::ARGUMENT_ERROR);
        }

        $window_id = $this -> request -> post('window_id');
        if (Common::check_empty($window_id) === true) {
            return Base::echo_error(Error::ARGUMENT_ERROR);
        }

        // 连接mongodb数据库
        $dbname = 'order_system_'.date('Y');
        $db = Base::connect_mongodb($dbname);
        // 连接表
        $collection = $db -> selectCollection(md5($window_id));
        $order_id = new \MongoId($order_id);
        $cursor = $collection -> findOne(array('_id' => $order_id));
        if ($cursor === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return Base::echo_success($cursor['order_info']);
    }
    // 根据条件查询订单
    public function choose_order () 
    {

        $window_id = $this -> request -> post('window_select');
        $date = $this -> request -> post('start');
        $meals_time_id = $this -> request -> post('meals_time_id');
        $meals_time_id = intval($meals_time_id);
        // 窗口为默认值 时间为空 饭点为默认值
        if ($window_id == 0 && Common::check_empty($date) === true && $meals_time_id == -1) {
            return $this -> show_orders();
        }
        // 窗口为默认值 时间有值 饭点为默认值
        if ($window_id == 0 && Common::check_empty($date) === false && $meals_time_id == -1) {
            $query = array('date' => $date);
            return $this -> show_orders($query);
        }
        // 窗口为默认值 时间为空 饭点有值
        if ($window_id == 0 && Common::check_empty($date) === true && $meals_time_id != -1) {
            $query = array('meals_time_id' => $meals_time_id);
            return $this -> show_orders($query);
        }
        // 窗口为默认值 时间有值 饭点为有值
        if ($window_id == 0 && Common::check_empty($date) === false && $meals_time_id != -1) {
            $query = array('date' => $date,'meals_time_id' => $meals_time_id);
            return $this -> show_orders($query);
        }
        // 窗口为有值 时间为空 饭点为默认值
        if ($window_id != 0 && Common::check_empty($date) === true && $meals_time_id == -1) {
            $query = array('window_id' => $window_id);
            return $this -> show_orders($query,$window_id);
        }

        // 窗口为有值 时间有值 饭点为默认值
        if ($window_id != 0 && Common::check_empty($date) === false && $meals_time_id == -1) {
            $query = array('window_id' => $window_id,'date' => $date);
            return $this -> show_orders($query,$window_id);
        }

        // 都有值的时候
        if ($window_id != 0 && Common::check_empty($date) === false && $meals_time_id != -1) {
            $query = array('window_id' => $window_id,'date' => $date,'meals_time_id' => $meals_time_id);
            return $this -> show_orders($query,$window_id);
        }

    }

}