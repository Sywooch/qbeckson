<?php

namespace app\tests\codeception\fixtures\organization;

use yii\test\ActiveFixture;

class OrganizationFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Organization';
    public $dataFile = '@tests/codeception/fixtures/data/organization/organization.php';
}