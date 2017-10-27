<?php

namespace app\tests\codeception\fixtures\contracts;

use yii\test\ActiveFixture;

class ContractsFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Contracts';
    public $dataFile = '@tests/codeception/fixtures/data/contracts/contracts.php';
}