<?php

namespace app\tests\codeception\fixtures\programmeModule;

use yii\test\ActiveFixture;

class ProgrammeModuleFixture extends ActiveFixture
{
    public $modelClass = 'app\models\ProgrammeModule';
    public $dataFile = '@tests/codeception/fixtures/data/programmeModule/programme_module.php';
}