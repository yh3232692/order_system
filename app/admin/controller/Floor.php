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

class Floor extends Base 
{    
    // 渲染页面
    public function index ()
    {
        return $this -> fetch();
    }

    // 为页面jqgrid分配数据
    public function jqgrid_all() 
    {
        $floor = Db::name('floor');
        $result = $floor -> select();
        if ($result === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return $this -> echo_success ($result);
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
    protected function add_or_update ($is_add)
    {
        $id = '';
        $floor = Db::name('floor');
        
        // 判断是否为添加并且检测id是否合法
        if($is_add === false) {
            $id = $this -> request -> post('id');
            if(Common::check_id($floor,$id) === true) {
                $data['id'] = $id;
            } else {
                return Common::check_id($floor,$id);
            }
        }

        // 检测 name是否合法
        $name = $this -> request -> post('name');        
        if(Common::check_name($floor, $name, $is_add, $id) === true) {
            $data['name'] = $name;                        
        } else {
            return Common::check_name($floor, $name, $is_add, $id);            
        }

        $description = $this -> request -> post('description');
        $data['description'] = $description;


        // 新增数据
        if ($is_add === true) {
            $data['id'] = Base::guid();
            if ($floor -> insert($data) == 1) {
                return Base::echo_success(Error::ADD_SUCCESS);
            } else {
                return Base::echo_error(Error::ADD_ERROR);
            }
        }

        // 更新数据
        if ($is_add === false) {
            if ($floor -> update($data) !== false) {
                return Base::echo_success(Error::UPDATE_SUCCESS);
            } else {
                return Base::echo_error(Error::UPDATE_ERROR);                
            }
        }
  
    }

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
        $floor = Db::name('floor');
        $window = Db::name('window');
        for ($i = 0; $i < count($idArr) ; $i++) { 
            $win_res = $window -> where('floor_id',$idArr[$i]) -> delete();
            if ($win_res !== false) {
                $res = $floor -> where('id',$idArr[$i]) -> delete();
                if (!$res) {
                    Db::rollback();
                    return Base::echo_error(Error::DELETE_ERROR);
                }
            }
            if ($win_res === false) {
                Db::rollback();
                return Base::echo_error(Error::DELETE_ERROR);
            }
        }
        // 提交事务            
        Db::commit();
        return Base::echo_success(Error::DELETE_SUCCESS);
    }

}
