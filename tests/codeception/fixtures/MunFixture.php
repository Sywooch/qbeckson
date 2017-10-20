<?php

namespace app\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class MunFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Mun';
    public $dataFile = '@tests/codeception/fixtures/data//mun.php';
}