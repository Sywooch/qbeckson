<?php

namespace app\tests\codeception\fixtures\operators;

use yii\test\ActiveFixture;

class OperatorsFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Operators';
    public $dataFile = '@tests/codeception/fixtures/data/operators//operators.php';
}