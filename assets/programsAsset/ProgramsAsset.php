<?php

namespace app\assets\programsAsset;

class ProgramsAsset extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__ . '/build/';
    public $baseUrl = '@web';
    public $css = [
        'css/newstyle.css',
    ];
    public $js = [
        'js/plugins/jquery.dotdotdot.js',
        'js/ui.js',
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];

}