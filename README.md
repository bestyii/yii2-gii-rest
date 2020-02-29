Yii2 RESTful generator for gii
======================
Gii 模块的REST代码生成器，包含Model和Controller。

代码注释文档包含OpenAPI  Specification 规范的文档。 

可配合 [Yii2 OpenApi Reader](https://github.com/bestyii/yii2-openapi-reader) 模块渲染出漂亮的api文档。

安装 Installation
------------

通过 [composer](http://getcomposer.org/download/)安装.

项目中直接运行

```
php composer.phar require --prefer-dist bestyii/yii2-gii-rest "*"
```

or add

```
"bestyii/yii2-gii-rest": "*"
```

或者添加下面代码到 `composer.json`文件


使用 Usage
-----

在`config/web.php` 配置文件中，`gii`的配置部分增加模版：
```php
$config['modules']['gii'] = [
        'class' => 'yii\gii\Module',

        'generators' => [ //自定义生成器
            'rest-model' => [ // generator name
                'class' => 'bestyii\giiRest\model\Generator', // generator class
            ],
            'rest-crud' => [ // generator name
                'class' => 'bestyii\giiRest\crud\Generator', // generator class
            ]
        ],
    ];
```
运行gii，可以看到增加了`REST Model Generator`和`REST CRUD Generator`两个生成器。
![alt gii](demo.png "gii")
