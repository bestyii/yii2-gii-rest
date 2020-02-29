<?php
/**
 * This is the template for generating a CRUD controller class file.
 */


use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();
$controllerName=str_replace('controller','',strtolower($controllerClass));
echo "<?php\n";
echo "/**\n";
echo "* REST URL config\n";
echo "*/\n";
echo "return [
    [
        'class' => 'yii\\rest\UrlRule',
        'controller' => 'v1/default',
        'only' => ['index'],
    ],
    [
        'class' => 'yii\\rest\UrlRule',
        'controller' => 'v1/$controllerName',
    ]
]";
