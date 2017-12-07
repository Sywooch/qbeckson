<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 04.12.17
 * Time: 16:12
 */
echo Html::a('submit', ['site/foobar'], [
    'data' => [
        'method' => 'post',
        'params' => [
            'name1' => 'value1',
            'name2' => 'value2',
        ],
    ],
]);
