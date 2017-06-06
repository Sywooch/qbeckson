<?php

use yii\db\Migration;

/**
 * Class m170606_100840_drop_directivity_column_from_programs
 */
class m170606_100840_drop_directivity_column_from_programs extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('programs', 'directivity');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return true;
    }
}
