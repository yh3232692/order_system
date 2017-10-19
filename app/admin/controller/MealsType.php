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

class MealsType extends Base 
{    
    // 渲染页面
    public function index ()
    {
        return $this -> fetch();
    }

    // 为页面jqgrid分配数据
    public function jqgrid_all() 
    {
        $meals_type = Db::name('meals_type');
        $result = $meals_type -> select();
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
        $meals_type = Db::name('meals_type');
        
        // 判断是否为添加并且检测id是否合法
        if($is_add === false) {
            $id = $this -> request -> post('id');
            if(Common::check_id($meals_type,$id) === true) {
                $data['id'] = $id;
            } else {
                return Common::check_id($meals_type,$id);
            }
        }

        // 检测 name是否合法
        $name = $this -> request -> post('name');        
        if(Common::check_name($meals_type, $name, $is_add, $id) === true) {
            $data['name'] = $name;                        
        } else {
            return Common::check_name($meals_type, $name, $is_add, $id);            
        }

        // 检测 name是否合法
        $code = $this -> request -> post('code');        
        if(Common::check_name($meals_type, $code, $is_add, $id) === true) {
            $data['code'] = $code;                        
        } else {
            return Common::check_name($meals_type, $code, $is_add, $id);            
        }


        // 新增数据
        if ($is_add === true) {
            $data['id'] = Base::guid();
            if ($meals_type -> insert($data) == 1) {
                return Base::echo_success(Error::ADD_SUCCESS);
            } else {
                return Base::echo_error(Error::ADD_ERROR);
            }
        }

        // 更新数据
        if ($is_add === false) {
            if ($meals_type -> update($data) !== false) {
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
        $meals_type = Db::name('meals_type');
        $cook_menu = Db::name('cook_menu');
        for ($i = 0; $i < count($idArr) ; $i++) { 
            $cook_res = $cook_menu -> where('meals_type_id',$idArr[$i]) -> delete();
            if ($cook_res !== false) {
                $res = $meals_type -> where('id',$idArr[$i]) -> delete();
                if (!$res) {
                    Db::rollback();
                    return Base::echo_error(Error::DELETE_ERROR);
                }
            }
            if ($cook_res === false) {
                Db::rollback();
                return Base::echo_error(Error::DELETE_ERROR);
            }
        }
        // 提交事务            
        Db::commit();
        return Base::echo_success(Error::DELETE_SUCCESS);
    }

}
