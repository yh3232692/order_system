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
namespace app\admin\model;

use think\Model;
use think\Db;
use think\Request;
use app\common\Base;
use app\common\Error;

class Common extends Model
{
    public static function check_empty($var) {
        if(isset($var) && $var !== '' ) {
            return false;
        }
        return true;
    }
    
    // 检测id
    public static function check_id ($dbname,$id) 
    {
        if(Common::check_empty($id)) {
            return Base::echo_error(Error::ID_IS_EMPTY);
        }
        $row = $dbname -> where("id",'=',"$id") -> select();
        if($row === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        if(count($row) === 0) {
            return Base::echo_error(Error::ID_NOT_EXIST);
        }
        return true;
    }

    // 检测name
    public static function check_name ($dbname,$name, $is_add, $id) 
    {
        if($is_add && Common::check_empty($name)) {
            return Base::echo_error(Error::NAME_IS_EMPTY);
        }

        if(Common::check_empty($name) === false) {
            if($is_add) {
                $row = $dbname -> where("name",'=',"$name") -> select();
            } else {
                $row = $dbname -> where("name = '$name' AND id != '$id'") -> select();
            }
            if ($row === false) {
                return Base::echo_error(Error::DB_ERROR);
            }
            if (count($row) != 0) {
                return Base::echo_error(Error::NAME_ALREADY_EXIST);
            }
            return true;
        }
    }
    
    // 检测ip
    public static function check_ip ($dbname,$ip, $is_add, $id) 
    {
        if($is_add && Common::check_empty($ip)) {
            return Base::echo_error(Error::IP_IS_EMPTY);
        }

        if(Common::check_empty($ip) === false) {
            if($is_add) {
                $row = $dbname -> where("ip",'=',"$ip") -> select();
            } else {
                $row = $dbname -> where("ip = '$ip' AND id != '$id'") -> select();
            }
            if ($row === false) {
                return Base::echo_error(Error::DB_ERROR);
            }
            if (count($row) != 0) {
                return Base::echo_error(Error::IP_ALREADY_EXIST);
            }
            return true;
        }
    }

    // 检测code
    public static function check_code ($dbname,$code, $is_add, $id) 
    {
        if($is_add && Common::check_empty($code)) {
            return Base::echo_error(Error::CODE_IS_EMPTY);
        }
        if(Common::check_empty($code) === false) {
            if($is_add) {
                $row = $dbname -> where("code",'=',"$code") -> select();
            } else {
                $row = $dbname -> where("code = '$code' AND id != '$id'") -> select();
            }
            if ($row === false) {
                return Base::echo_error(Error::DB_ERROR);
            }
            if (count($row) != 0) {
                return Base::echo_error(Error::CODE_ALREADY_EXIST);
            }
            return true;
        }
    }

     

    



    
}