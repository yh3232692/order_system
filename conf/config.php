<?php
    // 引入系统变量Env类
    use think\Env;

    return [
        // 是否开启项目的调试模式
        'app_debug'              => Env::get('app_debug'),
        // 入口自动绑定模块
        'auto_bind_module'       => true,
        // 视图输出字符串内容替换
        'view_replace_str'       => [
            '__CSS__'       =>  '/order_system/public/static/admin/css',
            '__JS__'        =>  '/order_system/public/static/admin/js',
            '__UPLOAD__'    =>  '/order_system/public/static/admin/upload',
        ],
        
    ];











?>