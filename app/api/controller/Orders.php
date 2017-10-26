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

class Orders extends Base 
{
    private $idArr = [];
    // 集中提交订单的接口
    public function add_orders () { 
        //  模拟窗口订单
        // $orderArr = [
        //     'window_id' => '1',
        //     'rows' => [
        //         [
        //             'window_id' => '1',
        //             'window_name' => '南湖餐厅-2楼-1号窗口',
        //             'date' => '2017-10-23',
        //             'meals_time_id' => 1,
        //             'meals_time_info' => '午餐',
        //             'student_id' => '1',
        //             'order_price' => 27,
        //             'order_time'  => '2017-10-23 09:10:38',
        //             'order_info' => [
        //                 [
        //                     'cook_menu_id' => '2642B811-C96B-4AE1-8DEF-1C282D73BC58',
        //                     'cook_menu_name' => '土豆丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '318D17BF-6E39-4FFC-9BA0-2B6FA209542F',
        //                     'cook_menu_name' => '鱼香肉丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '7BE554D0-0BDB-43DB-BC89-86A218AE8E18',
        //                     'cook_menu_name' => '米饭',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //             ]
        //         ], 
        //         [
        //             'window_id' => '1',
        //             'window_name' => '南湖餐厅-2楼-1号窗口',
        //             'date' => '2017-10-23',
        //             'meals_time_id' => 1,
        //             'meals_time_info' => '午餐',
        //             'student_id' => '2',
        //             'order_price' => 27,
        //             'order_time'  => '2017-10-23 09:10:38',
        //             'order_info' => [
        //                 [
        //                     'cook_menu_id' => '2642B811-C96B-4AE1-8DEF-1C282D73BC58',
        //                     'cook_menu_name' => '土豆丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '318D17BF-6E39-4FFC-9BA0-2B6FA209542F',
        //                     'cook_menu_name' => '鱼香肉丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '7BE554D0-0BDB-43DB-BC89-86A218AE8E18',
        //                     'cook_menu_name' => '米饭',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //             ]
        //         ],
        //         [
        //             'window_id' => '1',
        //             'window_name' => '南湖餐厅-2楼-1号窗口',
        //             'date' => '2017-10-23',
        //             'meals_time_id' => 1,
        //             'meals_time_info' => '午餐',
        //             'student_id' => '3',
        //             'order_price' => 27,
        //             'order_time'  => '2017-10-23 09:10:38',
        //             'order_info' => [
        //                 [
        //                     'cook_menu_id' => '2642B811-C96B-4AE1-8DEF-1C282D73BC58',
        //                     'cook_menu_name' => '土豆丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '318D17BF-6E39-4FFC-9BA0-2B6FA209542F',
        //                     'cook_menu_name' => '鱼香肉丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '7BE554D0-0BDB-43DB-BC89-86A218AE8E18',
        //                     'cook_menu_name' => '米饭',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //             ]
        //         ],  
        //         [
        //             'window_id' => '1',
        //             'window_name' => '南湖餐厅-2楼-1号窗口',
        //             'date' => '2017-10-23',
        //             'meals_time_id' => 1,
        //             'meals_time_info' => '午餐',
        //             'student_id' => '3',
        //             'order_price' => 27,
        //             'order_time'  => '2017-10-23 09:10:38',
        //             'order_info' => [
        //                 [
        //                     'cook_menu_id' => '2642B811-C96B-4AE1-8DEF-1C282D73BC58',
        //                     'cook_menu_name' => '土豆丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '318D17BF-6E39-4FFC-9BA0-2B6FA209542F',
        //                     'cook_menu_name' => '鱼香肉丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '7BE554D0-0BDB-43DB-BC89-86A218AE8E18',
        //                     'cook_menu_name' => '米饭',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //             ]
        //         ],
        //         [
        //             'window_id' => '1',
        //             'window_name' => '南湖餐厅-2楼-1号窗口',
        //             'date' => '2017-10-23',
        //             'meals_time_id' => 1,
        //             'meals_time_info' => '午餐',
        //             'student_id' => '3',
        //             'order_price' => 27,
        //             'order_time'  => '2017-10-23 09:10:38',
        //             'order_info' => [
        //                 [
        //                     'cook_menu_id' => '2642B811-C96B-4AE1-8DEF-1C282D73BC58',
        //                     'cook_menu_name' => '土豆丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '318D17BF-6E39-4FFC-9BA0-2B6FA209542F',
        //                     'cook_menu_name' => '鱼香肉丝',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //                 [
        //                     'cook_menu_id' => '7BE554D0-0BDB-43DB-BC89-86A218AE8E18',
        //                     'cook_menu_name' => '米饭',
        //                     'num' => 3,
        //                     'price' => 3,
        //                     'total_price' => 9,
        //                 ],
        //             ]
        //         ],
                
        //     ]
            
        // ];
        // echo json_encode($orderArr);
        // 将接收过来的json字符串转成数组
        $orderArr = json_decode($this -> request -> post('order_json'),true);
        
        // 连接mongodb数据库
        $dbname = 'order_system_'.date('Y');
        $db = Base::connect_mongodb($dbname);
        // 连接表
        $collection = $db -> selectCollection(md5($orderArr['window_id']));

        // 取到当前窗口订单
        $new_arr = $orderArr['rows'];
        // 如果this -> idArr已经存在数据，说明有未删除的数据，先进行删除
        if (count($this -> idArr) > 0) {
            for ($j=0; $j < count($this -> idArr); $j++) { 
                $order_id = new \MongoId($this -> idArr[$j]);
                $collection -> remove(array('_id' => $order_id));
            }  
        }

        // 如果接收过来的新数据大于限制条数则不能添加
        if (count($new_arr) > 5) {
            return Base::echo_error(Error::DATA_LENGTH_ERROR);
        }
        for ($i = 0; $i < count($new_arr); $i++) { 
            $result = $collection -> insert($new_arr[$i],array('w'=>false));
            // 添加成功保存id
            if ($result == 1) {
                $this -> idArr [] = $new_arr[$i]['_id'];
            }
            // 添加失败删除已经添加成功的订单
            if ($result == 0) {
                for ($j=0; $j < count($this -> idArr); $j++) { 
                    $order_id = new \MongoId($this -> idArr[$j]);
                    $collection -> remove(array('_id' => $order_id));
                }
                return Base::echo_error(Error::ADD_ERROR);
            }
        }
        // 添加成功删除添加成功的id
        $this -> idArr = [];
        return Base::echo_success(Error::ADD_SUCCESS);
    }

    // 预定机预定订单的接口
    public function add_reserve () 
    {
        // 预定机测试数据
        // $orderArr = [
        //     'window_id' => '1',
        //     'rows' => [
        //         'window_id' => '1',
        //         'window_name' => '南湖餐厅-2楼-1号窗口',
        //         'date' => '2017-10-23',
        //         'meals_time_id' => 1,
        //         'meals_time_info' => '午餐',
        //         'student_id' => '3',
        //         'order_price' => 27,
        //         'order_time'  => '2017-10-23 09:10:38',
        //         'order_info' => [
        //             [
        //                 'cook_menu_id' => '2642B811-C96B-4AE1-8DEF-1C282D73BC58',
        //                 'cook_menu_name' => '土豆丝',
        //                 'num' => 3,
        //                 'price' => 3,
        //                 'total_price' => 9,
        //             ],
        //             [
        //                 'cook_menu_id' => '318D17BF-6E39-4FFC-9BA0-2B6FA209542F',
        //                 'cook_menu_name' => '鱼香肉丝',
        //                 'num' => 3,
        //                 'price' => 3,
        //                 'total_price' => 9,
        //             ],
        //             [
        //                 'cook_menu_id' => '7BE554D0-0BDB-43DB-BC89-86A218AE8E18',
        //                 'cook_menu_name' => '米饭',
        //                 'num' => 3,
        //                 'price' => 3,
        //                 'total_price' => 9,
        //             ],
        //         ]
        //     ]
        // ];
        // echo json_encode($orderArr);
        // $orderArr = json_decode($this -> request -> post('order_json'),true);
        // 连接mongodb数据库
        $dbname = 'order_reserve_'.date('Y');
        $db = Base::connect_mongodb($dbname);
        // 连接表
        $collection = $db -> selectCollection(md5($orderArr['window_id']));
        // 添加预订信息
        $result = $collection -> insert($orderArr['rows'],array('w'=>false));

        if ($result == 1) {
            return Base::echo_success(Error::ADD_SUCCESS);
        } else {
            return Base::echo_error(Error::ADD_ERROR);
        }
    }

    // 取消预订的接口
    public function cancel_reserve() 
    {
        // 检测学生id是否合法
        $student_id = $this -> request -> post('student_id');
        if (Common::check_empty($student_id) === true) {
            return Base::echo_error(Error::STUDENT_ID_IS_EMPTY);
        }

        // 检测日期是否空
        $date = $this -> request -> post('date');
        if (Common::check_empty($date) === true) {
            return Base::echo_error(Error::DATE_IS_EMPTY);
        }

        // 检测预定哪一餐的id不能为空
        $meals_time_id = $this -> request -> post('meals_time_id');
        if (Common::check_empty($meals_time_id) === true) {
            return Base::echo_error(Error::MEALS_TIME_ID_IS_EMPTY);
        }
        $meals_time_id = intval($meals_time_id);

        //检测window_id是否合法
        $window_id = $this -> request -> post('window_id');
        if (Common::check_empty($window_id) === true) {
            return Base::echo_error(Error::WINDOW_ID_IS_EMPTY);
        }

        // 连接mongodb数据库
        $dbname = 'order_reserve_'.date('Y');
        $db = Base::connect_mongodb($dbname);
        // 连接表
        $collection = $db -> selectCollection(md5($window_id));
        
        // 组织查询条件
        $query = array("date" => $date,"student_id" => $student_id,"meals_time_id" => $meals_time_id);
        $result = $collection -> findOne($query);

        if (count($result) > 0) {
            $res = $collection -> remove($query);
            return Base::echo_success(Error::ORDER_CANCEL_SUCCESS);
        } else {
            return Base::echo_error(Error::ORDER_ID_NOT_EXIST);
        }
    }

    // 查询预定订单是否存在
    protected function reserve_is_exist () 
    {
        // 检测学生id是否合法
        $student_id = $this -> request -> post('student_id');
        if (Common::check_empty($student_id) === true) {
            return Base::echo_error(Error::STUDENT_ID_IS_EMPTY);
        }

        // 检测日期是否空
        $date = $this -> request -> post('date');
        if (Common::check_empty($date) === true) {
            return Base::echo_error(Error::DATE_IS_EMPTY);
        }

        // 检测预定哪一餐的id不能为空
        $meals_time_id = $this -> request -> post('meals_time_id');
        if (Common::check_empty($meals_time_id) === true) {
            return Base::echo_error(Error::MEALS_TIME_ID_IS_EMPTY);
        }
        $meals_time_id = intval($meals_time_id);

        //检测window_id是否合法
        $window_id = $this -> request -> post('window_id');
        if (Common::check_empty($window_id) === true) {
            return Base::echo_error(Error::WINDOW_ID_IS_EMPTY);
        }

        // 连接mongodb数据库
        $dbname = 'order_reserve_'.date('Y');
        $db = Base::connect_mongodb($dbname);
        // 连接表
        $collection = $db -> selectCollection(md5($window_id));
        
        // 组织查询条件
        $query = array("date" => $date,"student_id" => $student_id,"meals_time_id" => $meals_time_id);
        $result = $collection -> findOne($query);
        return $result;
    }

    // 是否预定的接口
    public function is_reserve() 
    {
        $result = $this -> reserve_is_exist();
        if (count($result) > 0) {
            return Base::echo_error(Error::ORDER_IS_RESERVE);
        }
        return Base::echo_success(Error::YOU_CAN_RESERVE);
    }

    // 查询学生预定的订单
    public function show_student_reserve() 
    {
        $result = $this -> reserve_is_exist();
        if (count($result) > 0) {
            return Base::echo_success($result);
        }
        if ($result === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return Base::echo_error(Error::ORDER_ID_NOT_EXIST);
    }

 }