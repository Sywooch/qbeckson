<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 30.10.2017
 * Time: 17:05
 */

namespace app\tests\codeception\unit\models\mailing;

use app\models\mailing\services\MailingTaskGenerator;
use Codeception\Test\Unit;

class MailingTaskGeneratorTest extends Unit
{
    protected $generator;

    public function _before()
    {
        $this->generator = new MailingTaskGenerator(MailTask::class);
    }

    public function testBaseGenerator()
    {
        $this->markTestSkipped('надо бы отвязать от AR всю эту систему, а надо ли?');
        $i = 0;
        foreach ($this->generator as $task) {
            $i++;
            $this->assertEquals(1, $task);
        }

        $this->assertEquals(4, $i);
    }
}
