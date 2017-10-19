<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 19.10.2017
 * Time: 12:54
 */

namespace app\tests\codeception\unit\models\certificate;


use Codeception\Test\Unit;

class CertificateNerfNominalTest extends Unit
{
    use  \yii\test\FixtureTrait;
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function fixtures()
    {
        return [
            // 'certificates' => app\tests\fixtures\certificate\CertificatesFixture::className(),
        ];
    }

    public function testSomeFeature()
    {
        $this->markTestSkipped();
    }

    protected function _before()
    {
    }

    // tests

    protected function _after()
    {
    }
}