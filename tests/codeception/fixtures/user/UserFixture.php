<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 19.10.2017
 * Time: 12:48
 */

namespace app\tests\codeception\fixtures\user;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';
    public $dataFile = '@tests/codeception/fixtures/data/user/user.php';
}