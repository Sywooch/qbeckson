<?php
/**
 * Created by PhpStorm.
 * User: eugene-kei
 * Date: 20.12.17
 * Time: 19:10
 */
namespace app\assets\operatorDeleteContractAsset;

use yii\web\AssetBundle;

class OperatorDeleteContractAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/src/';
    public $baseUrl = '@web';
    public $js = [
        'js/operator-delete-contract.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}