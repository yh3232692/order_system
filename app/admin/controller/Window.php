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

class Window extends Base 
{
    private $sql = " SELECT a.*,CONCAT(b.`name`,'-',c.`name`,'-',a.`code`,'号窗口') AS name_info,
                    b.`name` AS dinner_name,b.address AS dinner_address,c.`name` AS floor_name 
                    FROM window a LEFT JOIN dinner b ON a.dinner_id = b.id LEFT JOIN floor c ON a.floor_id = c.id ORDER BY name_info asc";
    
    // 渲染页面
    public function index ()
    {
        return $this -> fetch();
    }

    // 为页面jqgrid分配数据
    public function jqgrid_all() 
    {
        $window = Db::name('window');
        $result = $window -> query($this -> sql);
        if ($result === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return $this -> echo_success ($result);
    }

    // 检测餐厅id
    protected function check_dinner_id ($dinner_id) 
    {
        $dbname = Db::name('dinner');
        if(Common::check_empty($dinner_id)) {
            return Base::echo_error(Error::DINNER_ID_IS_EMPTY);
        }
        $row = $dbname -> where("id",'=',"$dinner_id") -> select();
        if($row === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        if(count($row) === 0) {
            return Base::echo_error(Error::DINNER_ID_NOT_EXIST);
        }
        return true;
    }
    // 检测楼层id
    protected function check_floor_id ($floor_id) 
    {
        $dbname = Db::name('floor');
        if(Common::check_empty($floor_id)) {
            return Base::echo_error(Error::FLOOR_ID_IS_EMPTY);
        }
        $row = $dbname -> where("id",'=',"$floor_id") -> select();
        if($row === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        if(count($row) === 0) {
            return Base::echo_error(Error::FLOOR_ID_NOT_EXIST);
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
        $window = Db::name('window');

        // 判断是否为添加并且检测id是否合法
        $id = $this -> request -> post('id');        
        if($is_add === false) {
            if(Common::check_id($window,$id) === true) {
                $data['id'] = $id;
            } else {
                return Common::check_id($window,$id);
            }
        }
        
        // 检测 ip是否合法
        $ip = $this -> request -> post('ip');
        if(Common::check_ip($window, $ip, $is_add, $id) === true) {
            $data['ip'] = $ip;       
        } else {
            return Common::check_ip($window, $ip, $is_add, $id);            
        }

        // 检测 code是否合法
        $code = $this -> request -> post('code');        
        if(Common::check_code($window, $code, $is_add, $id) === true) {
            $data['code'] = $code;                        
        } else {
            return Common::check_code($window, $code, $is_add, $id);            
        }

        // 检测餐厅是否合法
        $dinner_id = $this -> request -> post('dinner_id');
        
        if($this -> check_dinner_id($dinner_id) === true) {
            $data['dinner_id'] = $dinner_id;
        } else {
            return $this -> check_dinner_id($dinner_id);
        }

        // 检测楼层是否合法
        $floor_id = $this -> request -> post('floor_id');
        if($this -> check_floor_id($floor_id) === true) {
            $data['floor_id'] = $floor_id;
        } else {
            return $this -> check_floor_id($floor_id);
        }

        $description = $this -> request -> post('description');
        $data['description'] = $description;

        // 新增数据
        if ($is_add === true) {
            if (Common::check_empty($id) === true) {
                return Base::echo_error(Error::ID_IS_EMPTY);
            } else {
                $data['id'] = $id;
            }
            if ($window -> insert($data) == 1) {
                return Base::echo_success(Error::ADD_SUCCESS);
            } else {
                return Base::echo_error(Error::ADD_ERROR);
            }
        }

        // 更新数据
        if ($is_add === false) {
            if ($window -> update($data) !== false) {
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

    // 筛选条件
    public function choose_data ()
    {
        $window = Db::name('window');
        // 检测餐厅是否合法
        $dinner_id = $this -> request -> post('dinner_id');        
        if (Common::check_empty($dinner_id) === true) {
            return Base::echo_error(Error::DINNER_ID_IS_EMPTY);
        } 

        // 检测楼层是否合法
        $floor_id = $this -> request -> post('floor_id');        
        if (Common::check_empty($floor_id) === true) {
            return Base::echo_error(Error::FLOOR_ID_IS_EMPTY);
        } 
        
        if ($dinner_id == 0 && $floor_id == 0) {
            $result = $window -> query($this -> sql);
        } 
        if ($dinner_id != 0 && $floor_id != 0) {
            $result = $window -> query($this -> sql."WHERE b.id = {$dinner_id} AND c.id = {$floor_id}");
        }
        if ($dinner_id == 0 && $floor_id != 0) {
            $result = $window -> query($this -> sql."WHERE c.id = {$floor_id}");
        }
        if ($dinner_id != 0 && $floor_id == 0) {
            $result = $window -> query($this -> sql."WHERE b.id = {$dinner_id}");
        }
        if ($result === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return Base::echo_success($result);
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
        $window = Db::name('window');
        for ($i = 0; $i < count($idArr) ; $i++) { 
            $res = $window -> where('id',$idArr[$i]) -> delete();
            
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
