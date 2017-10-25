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

class CookMenu extends Base 
{    
    private $sql = " SELECT a.id,a.advice_price,a.`code`,a.description,a.meals_type_id,a.`name`,
                    CONCAT(\"<img style='width:60%' src = '../static/upload/cook_menu/\",a.img_url,\"' />\") 
                    AS img_url,b.`name` AS meals_type_name FROM cook_menu a LEFT JOIN meals_type b ON a.meals_type_id = b.id ";
    // 渲染页面
    public function index ()
    {
        return $this -> fetch();
    }

    // 为页面jqgrid分配数据
    public function jqgrid_all() 
    {
        $cook_menu = Db::name('cook_menu');
        $result = $cook_menu -> query($this -> sql);
        if ($result === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        return $this -> echo_success ($result);
    }

   // 获取菜品分类
   public function get_meals_type ()
   {
       $dbname = Db::name('meals_type');
       $row = $dbname -> select();
       if ($row === false) {
           return Base::echo_error(Error::DB_ERROR);
       }
       return Base::echo_success($row);
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

    // 检测菜品分类id
    protected function check_meals_type_id ($meals_type_id) 
    {
        $dbname = Db::name('meals_type');
        if(Common::check_empty($meals_type_id)) {
            return Base::echo_error(Error::MEALS_TYPE_ID_IS_EMPTY);
        }
        $row = $dbname -> where("id",'=',"$meals_type_id") -> select();
        if($row === false) {
            return Base::echo_error(Error::DB_ERROR);
        }
        if(count($row) === 0) {
            return Base::echo_error(Error::MEALS_TYPE_ID_NOT_EXIST);
        }
        return true;
    }
    //添加或更新
    protected function add_or_update ($is_add)
    {
        $id = '';
        $cook_menu = Db::name('cook_menu');
        
        // 判断是否为添加并且检测id是否合法
        if($is_add === false) {
            $id = $this -> request -> post('id');
            if(Common::check_id($cook_menu,$id) === true) {
                $data['id'] = $id;
            } else {
                return Common::check_id($cook_menu,$id);
            }
        }

        // 检测 name是否合法
        $name = $this -> request -> post('name');        
        if(Common::check_name($cook_menu, $name, $is_add, $id) === true) {
            $data['name'] = $name;                        
        } else {
            return Common::check_name($cook_menu, $name, $is_add, $id);            
        }

        // 检测 code是否合法
        $code = $this -> request -> post('code');        
        if(Common::check_code($cook_menu, $code, $is_add, $id) === true) {
            $data['code'] = $code;                        
        } else {
            return Common::check_code($cook_menu, $code, $is_add, $id);            
        }

        // 检测 price是否合法
        $price = $this -> request -> post('advice_price');        
        if(Common::check_empty($price) === false) {
            $data['advice_price'] = $price;       
        } else {
            return Base::echo_error(Error::PRICE_IS_EMPTY);        
        }

        // 检测菜品分类是否合法
        $meals_type_id = $this -> request -> post('meals_type_id');
        
        if($this -> check_meals_type_id($meals_type_id) === true) {
            $data['meals_type_id'] = $meals_type_id;
        } else {
            return $this -> check_meals_type_id($meals_type_id);
        }

        $description = $this -> request -> post('description');
        $data['description'] = $description;

        // 检测图片是否合法
        
        $base64_image_content = $this -> request -> post('base64_data');
        if (Common::check_empty($base64_image_content) === false) {

            if ($is_add === false) {
                // 删除图片
                $img_name = $cook_menu -> where('id',$data['id']) -> field('img_url') ->find();
                $old_file = './static/upload/cook_menu/'.$img_name['img_url'];
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
            if ($cook_menu -> insert($data) == 1) {
                return Base::echo_success(Error::ADD_SUCCESS);
            } else {
                return Base::echo_error(Error::ADD_ERROR);
            }
        }

        // 更新数据
        if ($is_add === false) {
            if ($cook_menu -> update($data) !== false) {
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
        $garnish = Db::name('garnish');
        $cook_menu = Db::name('cook_menu');
        for ($i = 0; $i < count($idArr) ; $i++) { 
            // 删除关联菜类型的菜品
            $garnish_res = $garnish -> where('cook_menu_id',$idArr[$i]) -> delete();

            if ($garnish_res !== false) {
                // 删除图片
                $img_name = $cook_menu -> where('id',$idArr[$i]) -> field('img_url') ->find();
                $old_file = './static/upload/cook_menu/'.$img_name['img_url']; 

                if (is_file($old_file)) {
                    if (unlink ($old_file)) {
                        $res = $cook_menu -> where('id',$idArr[$i]) -> delete();
                        if (!$res) {
                            Db::rollback();
                            return Base::echo_error(Error::DELETE_ERROR);
                        }
                    }
                } else {
                    $res = $cook_menu -> where('id',$idArr[$i]) -> delete();
                    if (!$res) {
                        Db::rollback();
                        return Base::echo_error(Error::DELETE_ERROR);
                    }
                }
            }
            if ($garnish_res === false) {
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
            $new_file = "./static/upload/cook_menu/";

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


}
