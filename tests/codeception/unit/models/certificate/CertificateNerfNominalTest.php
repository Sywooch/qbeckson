<?php

namespace app\tests\codeception\unit\models\certificate;


use app\models\Certificates;
use app\models\certificates\CertificateNerfNominal;
use app\tests\codeception\fixtures\certificate\CertGroupFixture;
use app\tests\codeception\fixtures\certificate\CertificatesFixture;
use app\tests\codeception\fixtures\contracts\ContractsFixture;
use app\tests\codeception\fixtures\MunFixture;
use app\tests\codeception\fixtures\operators\OperatorsFixture;
use app\tests\codeception\fixtures\organization\OrganizationFixture;
use app\tests\codeception\fixtures\programs\ProgramsFixture;
use app\tests\codeception\fixtures\user\UserFixture;
use Codeception\Test\Unit;


/**
 * @property CertificateNerfNominal $nerfer
 * @property CertificateNerfNominal $nerferSpecail
 *
 */
class CertificateNerfNominalTest extends Unit
{
    use  \yii\test\FixtureTrait;
    /**
     * @var \UnitTester
     */
    const CERTIFICATE_PF_ID = 25;
    const CERTIFICATE_SPECIAL_ID = 35;

    protected $tester;

    protected $nerfer;
    protected $nerferSpecail;

    public function _fixtures()
    {
        return [
            'certificates' => CertificatesFixture::className(),
            'cert_group' => CertGroupFixture::className(),
            'user' => UserFixture::className(),
            'operators' => OperatorsFixture::className(),
            'mun' => MunFixture::className(),
            'organization' => OrganizationFixture::className(),
            'programs' => ProgramsFixture::className(),
            'contracts' => ContractsFixture::className(),
        ];
    }

    protected function _before()
    {
        $this->nerfer = new CertificateNerfNominal(self::CERTIFICATE_PF_ID);
        $this->nerferSpecail = new CertificateNerfNominal(self::CERTIFICATE_SPECIAL_ID);
    }

    // tests

    protected function _after()
    {

    }


    public function testCertInitIsDone()
    {
        expect('прошла инициализация, в нерфере модель сертификата',
            $this->nerfer->certificate instanceof Certificates)->true();
        expect('прошла инициализация, id тот что указан',
            $this->nerfer->certificate->id)->equals(self::CERTIFICATE_PF_ID);
        expect('прошла инициализация, в нерфере модель сертификата учета',
            $this->nerferSpecail->certificate instanceof Certificates)->true();
        expect('прошла инициализация сертификата учета, id тот что указан',
            $this->nerferSpecail->certificate->id)->equals(self::CERTIFICATE_SPECIAL_ID);
    }

    public function testSpecialCanNotNerfCertificate()
    {
        expect('валидация ПФ сертификата, нерфить можно(по типу сертификата)',
            $this->nerfer->validate(['certificate']))->true();
        expect('валидация сертификата учета, нерфить нельзя(по типу сертификата)',
            $this->nerferSpecail->validate(['certificate']))->false();
    }


    public function testBallanceValidate()
    {
        $this->nerfer->certificate->balance = 555;
        $this->nerfer->certificate->nominal = 555;
        $this->assertTrue($this->nerfer->validate(['certificate']),
            'валидация ПФ сертификата, баланс должен быть равен номиналу');
        $this->nerfer->certificate->balance = 999;
        $this->nerfer->certificate->nominal = 555;
        expect('валидация ПФ сертификата, баланс должен быть равен номиналу, а если не равен',
            $this->nerfer->validate(['certificate']))->false();


    }

    public function testContractsValidate()
    {
        //   expect('без контрактов валиден', $this->nerfer->validate())->true();
        $this->markTestIncomplete();

        //expect('с активными контрактами нет', $this->nerfer->validate())->false();

    }


}