<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 15.09.2017
 * Time: 12:17
 */

namespace app\assets;


use yii\web\AssetBundle;

class AngularAsset extends AssetBundle
{
    public $sourcePath = '@bower/angular';
    public $js = [
        'angular.min.js',
    ];
    public $publishOptions = [
        'only' => [
            'angular.min.js',
        ],
    ];

}