<?php

namespace app\tests\codeception\fixtures\programs;

use yii\test\ActiveFixture;

class ProgramsFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Programs';
    public $dataFile = '@tests/codeception/fixtures/data/programs/programs.php';
}