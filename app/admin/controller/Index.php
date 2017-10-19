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
use app\common\Base;
use app\common\Error;
use think\Controller;
use think\Db;
use think\Request;
use think\Env;

class Index extends Base 
{   
    public function index() 
    {
        return $this -> fetch();
    }
    public function home_page()
    {
        return $this -> fetch();
    }
}