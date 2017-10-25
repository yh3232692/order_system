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
        foreach ($result as $key => &$value) {
            $value['img_url'] = "<img style='width:30%' src = '../static/upload/meals_type/".$value['img_url']."' />";
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

        // 检测 code是否合法
        $code = $this -> request -> post('code');        
        if(Common::check_code($meals_type, $code, $is_add, $id) === true) {
            $data['code'] = $code;                        
        } else {
            return Common::check_code($meals_type, $code, $is_add, $id);            
        }

        // 检测图片是否合法
        
        $base64_image_content = $this -> request -> post('base64_data');
        if (Common::check_empty($base64_image_content) === false) {

            if ($is_add === false) {
                // 删除图片
                $img_name = $meals_type -> where('id',$data['id']) -> field('img_url') ->find();
                $old_file = './static/upload/meals_type/'.$img_name['img_url'];
                if (is_file($old_file)) {
                    unlink ($old_file); 
                    if ($this -> upload($base64_image_content) === false) {
                        return Base::echo_error(Error::UPLOAD_ERROR);
                    } 
                    
                    $data['img_url'] = $this -> upload($base64_image_content); 
                }
            }
            
            if ($this -> upload($base64_image_content) === false) {
                return Base::echo_error(Error::UPLOAD_ERROR);
            } 
            
            $data['img_url'] = $this -> upload($base64_image_content);
            
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
            return Base::echo_error(Error::ARGUMENT_ERROR);
        }
        $ids = substr($ids,0,-1);
        $idArr = explode(',',$ids);

        // 启动事务
        Db::startTrans();
        $meals_type = Db::name('meals_type');
        $cook_menu = Db::name('cook_menu');
        for ($i = 0; $i < count($idArr) ; $i++) { 
            // 删除关联菜类型的菜品
            $cook_res = $cook_menu -> where('meals_type_id',$idArr[$i]) -> delete();

            if ($cook_res !== false) {
                // 删除图片
                $img_name = $meals_type -> where('id',$idArr[$i]) -> field('img_url') ->find();
                $old_file = './static/upload/meals_type/'.$img_name['img_url']; 

                if (is_file($old_file)) {
                    if (unlink ($old_file)) {
                        $res = $meals_type -> where('id',$idArr[$i]) -> delete();
                        if (!$res) {
                            Db::rollback();
                            return Base::echo_error(Error::DELETE_ERROR);
                        }
                    }
                } else {
                    $res = $meals_type -> where('id',$idArr[$i]) -> delete();
                    if (!$res) {
                        Db::rollback();
                        return Base::echo_error(Error::DELETE_ERROR);
                    }
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

    public function upload ($base64_image_content) {
        
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){

            $type = $result[2];
            $new_file = "./static/upload/meals_type/";

            if(!is_dir($new_file))
            {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0700);
            }
            $img_name = 'mt_'.time().".{$type}";
            $new_file = $new_file.$img_name;
            
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                return $img_name;
            }else{
                
                return false;
            }
        }
    }
    public function test () {
        $meals_type = Db::name('meals_type');
        $img_name = $meals_type -> where('id','4C9AAB68-7AF3-4BA0-B5DD-C5B78780B472') -> field('img_url') ->find();
        dump($img_name) ;
    }




}
