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

class Statistic extends Base 
{
    public function test () 
    {
        $window = Db::name('window');
        $tableArr = $window -> field('id') ->select();
        if ($tableArr === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        // 连接mongo数据库
        $dbname = 'order_system_'.date('Y');    
        $db = Base::connect_mongodb($dbname);   
        for ($i = 0; $i < count($tableArr); $i++) { 
            $collection = $db -> selectCollection(md5($tableArr[$i]['id']));

        }
    }

    // 窗口当天销售统计
    public function statistic_window ()
    {
        // $idArr = ['2642B811-C96B-4AE1-8DEF-1C282D73BC58','318D17BF-6E39-4FFC-9BA0-2B6FA209542F','7BE554D0-0BDB-43DB-BC89-86A218AE8E18'];
        // $window_id = '1';
        // $date = '2017-10-23';
       
        // 检测日期是否空
        $date = $this -> request -> post('date');
        if (Common::check_empty($date) === true) {
            return Base::echo_error(Error::DATE_IS_EMPTY);
        }

        //检测window_id是否合法
        $window_id = $this -> request -> post('window_id');
        if (Common::check_empty($window_id) === true) {
            return Base::echo_error(Error::WINDOW_ID_IS_EMPTY);
        }

        // 检测菜的ids是否空
        $ids = $this -> request -> post('ids');
        if (Common::check_empty($ids)) {
            return Base::echo_error(Error::ARGUMENT_ERROR);
        }
        $ids = substr($ids,0,-1);
        $idArr = explode(',',$ids);
        
        // 连接mongo数据库
        $dbname = 'order_system_'.date('Y');    
        $db = Base::connect_mongodb($dbname);
        $collection = $db -> selectCollection(md5($window_id));

        // 查询满足条件的数据
        $cursor = $collection -> find(array('date' => $date)) -> fields(array('order_info' => true));
        if ($cursor === false) {
            return Base::echo_error(Error::DB_ERROR);
        }

        // 创建数组用来接收单个菜的数据
        $cook_row = [];
        foreach ($cursor as $doc) {
            foreach ($doc['order_info'] as $key => $docVal) {
                $cook_row[] = $docVal;
            }
        }
        
        // 用来接收整理所需要的数据格式
        // 格式为 [['cook_menu_id'=>'',....],[]...]
        $row = [];
        foreach ($idArr as $idkey => $idVal) {
            $num = 0;               //记录每个菜的销售总数量
            $total_price = 0;       //记录单个菜的总价
            foreach ($cook_row as $key => $value) {
                if ($value['cook_menu_id'] == $idVal) {
                    // 满足条件进行累加的运算
                    $num += $value['num'];
                    $total_price += $value['total_price'];
                    $row[$idkey] = [
                        'cook_menu_id' => $value['cook_menu_id'],
                        'cook_menu_name' => $value['cook_menu_name'],
                        'num' => $num,
                        'total_price' => $total_price
                    ];
                }
            }
        }
        if (count($row) == 0) {
            return Base::echo_error('本窗口当天没有可结算的订单');
        }
        if (count($row) > 0) {
            return Base::echo_success($row);
        }
        
    }

}