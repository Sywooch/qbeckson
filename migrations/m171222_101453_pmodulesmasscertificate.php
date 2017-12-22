<?php

use yii\db\Migration;

class m171222_101453_pmodulesmasscertificate extends Migration
{
    public function safeUp()
    {
        \app\models\ProgrammeModule::updateAll(
            ['verification' => \app\models\ProgrammeModule::VERIFICATION_DONE],
            [
                'program_id' => \app\models\Programs::find()->select(['id'])
                    ->where([
                        'verification' => \app\models\Programs::VERIFICATION_DONE
                    ])
            ]
        );
    }

    public function safeDown()
    {
        echo "m171222_101453_pmodulesmasscertificate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171222_101453_pmodulesmasscertificate cannot be reverted.\n";

        return false;
    }
    */
}
