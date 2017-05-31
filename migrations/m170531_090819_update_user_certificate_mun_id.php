<?php

use yii\db\Migration;

class m170531_090819_update_user_certificate_mun_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute('
            UPDATE user
            INNER JOIN certificates ON certificates.user_id = user.id
            INNER JOIN payers ON certificates.payer_id = payers.id
            SET user.mun_id = payers.mun
        ');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute('UPDATE user SET mun_id = NULL');
    }
}
