# think-twig
tp6 twig模板

#安装方式
```
composer require rain-sjh/think-twig
```

安装完成后请在config配置文件view.php进行一下配置
    
    return [
        // 模板引擎类型使用 Think
        'type'          => 'Twig',
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
        'auto_rule'     => 1,
        // 模板目录名
        'view_dir_name' => 'view',
        // 模板后缀
        'view_suffix'   => 'twig',
        // 模板文件名分隔符
        'view_depr'     => DIRECTORY_SEPARATOR,
        // 模板引擎普通标签开始标记
        'tpl_begin'     => '{{',
        // 模板引擎普通标签结束标记
        'tpl_end'       => '}}',
        // 标签库标签开始标记
        'taglib_begin'  => '{%',
        // 标签库标签结束标记
        'taglib_end'    => '%}',
    ];

如需拓展twig模板函数,请在extend下创建