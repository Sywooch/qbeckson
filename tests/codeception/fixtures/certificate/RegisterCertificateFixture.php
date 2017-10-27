<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 19.10.2017
 * Time: 12:47
 */

namespace app\tests\codeception\fixtures\certificate;

use yii\test\ActiveFixture;

class RegisterCertificateFixture extends ActiveFixture
{
    public $modelClass = 'app\models\Certificates';
    public $dataFile = '@tests/codeception/fixtures/data/certificate/registerCertificates.php';
}