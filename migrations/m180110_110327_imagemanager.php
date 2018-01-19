<?php

use yii\db\Migration;

class m180110_110327_imagemanager extends Migration
{
    public function safeUp()
    {
        $command = <<<SHELL
    php yii migrate --migrationPath=@noam148/imagemanager/migrations --interactive=0
SHELL;
        exec($command);
    }

    public function safeDown()
    {
        return true;
    }

}
