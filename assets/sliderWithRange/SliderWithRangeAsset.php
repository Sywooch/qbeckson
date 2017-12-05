<?php

namespace app\assets\sliderWithRange;

use yii\web\AssetBundle;

class SliderWithRangeAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/js/';
    public $baseUrl = '@web';

    public $js = [
        'sliderWithRange.js',
    ];

    public $depends = [
        'kartik\field\FieldRangeAsset',
        'kartik\slider\SliderAsset'
    ];

}