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

        $result = $garnish -> query($this -> sql."WHERE b.id = '$window_id'");

        if ($result === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return $this -> echo_success ($result);
    }





}

