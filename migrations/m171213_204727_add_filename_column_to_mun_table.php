<?php

use yii\db\Migration;

/**
 * Handles adding filename to table `mun`.
 */
class m171213_204727_add_filename_column_to_mun_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('mun', 'filename', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('mun', 'filename');
    }
}
