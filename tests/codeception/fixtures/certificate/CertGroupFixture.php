<?php

namespace app\tests\codeception\fixtures\certificate;

use yii\test\ActiveFixture;

class CertGroupFixture extends ActiveFixture
{
    public $modelClass = 'app\models\CertGroup';
    public $dataFile = '@tests/codeception/fixtures/data/certificate/cert_group.php';
}