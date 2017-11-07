<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 19.10.2017
 * Time: 12:44
 */

namespace app\tests\codeception\unit\models\certificate;

use app\models\Certificates;
use app\models\User;
use app\tests\codeception\fixtures\certificate\CertificatesFixture;
use app\tests\codeception\fixtures\user\UserFixture;
use Codeception\Test\Unit;


class CertificateBaseValidationTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $users = [];


    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                // fixture data located in tests/_data/user.php
                //'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'certificates' => [
                'class' => CertificatesFixture::className()
            ]
        ];
    }

    public function testBaseValidateUser()
    {
        $user = new User([
            'username' => '',
            'password' => '1234567',
        ]);

        expect('не валидируется без имени', $user->validate())->false();

        $user->username = 'w';

        expect('не валидируется c коротким именем', $user->validate())->false();

        $user->username = 'wwwww';
        $user->password = 'qwer';
        expect('не валидируется c коротким паролем', $user->validate())->false();

        $user->username = '555444';
        $user->password = 'qwerty';
        expect('не валидируется с не уникальным именем', $user->validate())->false();

        $user->username = 'wwwww';
        $user->password = 'qwerty';
        expect('сохраняется', $user->save())->true();
    }

    public function testRequiredValidateCertificate()
    {
        //$this->markTestIncomplete();
        $certificate = new Certificates();

        expect('все поля пустые, валидация не проходит', $certificate->validate())->false();
        expect('name в списке ошибок', $certificate->getErrors('name'))->notEmpty();
        expect('soname в списке ошибок', $certificate->getErrors('soname'))->notEmpty();

        expect(
            'possible_cert_group в списке ошибок',
            $certificate->getErrors('possible_cert_group')
        )->notEmpty();
        expect('selectCertGroup в списке ошибок', $certificate->getErrors('selectCertGroup'))->notEmpty();


        /* expect('эти поля "\'name\', \'soname\', \'possible_cert_group\' ,\'selectCertGroup\'" в списке ошибок',
            array_keys($certificate->getErrors()))->contains(['name', 'soname', 'possible_cert_group','selectCertGroup']);*/


    }

    protected function _before()
    {
        $this->users['wrongName'] = [
            'username' => '555444',

        ];

    }

    protected function _after()
    {
    }

    // tests
//    public function testSomeFeature()
//    {
//        \Codeception\Util\Debug::debug(print_r(\app\models\User::find()->asArray()->all(), true));
//     //   \Codeception\Util\Debug::debug(print_r($this->tester->grabFixtures(), true));
//        expect('has elements',count(\app\models\User::findAll('1=1')))->equals(1);
//        ;
//    }
}
