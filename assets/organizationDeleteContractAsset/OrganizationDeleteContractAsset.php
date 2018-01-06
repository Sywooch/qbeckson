<?php
/**
 * Created by PhpStorm.
 * User: eugene-kei
 * Date: 20.12.17
 * Time: 1:16
 */
namespace app\assets\organizationDeleteContractAsset;

use yii\web\AssetBundle;

class OrganizationDeleteContractAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/src/';
    public $baseUrl = '@web';
    public $js = [
        'js/organization-delete-contract.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}