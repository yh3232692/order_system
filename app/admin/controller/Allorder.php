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
    
   
    public function show_orders () {
        // 连接mongodb数据库
        $db = Base::connect_mongodb();
        // 根据表查询所有订单
        $collection = $db -> selectCollection(md5('2')); 
        // 返回游标
        $cursor = $collection -> find();
        // 处理游标数据
        $row = [];
        foreach ($cursor as $doc) {
            $row[] = $doc;
        }
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }

}