<?php

namespace app\tests\codeception\fixtures\payers;

use yii\test\ActiveFixture;

class PayersFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Payers';
    public $dataFile = '@tests/codeception/fixtures/data/contracts/payers.php';
}