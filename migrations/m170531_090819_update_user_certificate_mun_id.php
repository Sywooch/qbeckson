<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170531_090819_update_user_certificate_mun_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        /** @var UserIdentity[] $users */
        $users = UserIdentity::find()->joinWith(['certificate'])->all();

        foreach ($users as $user) {
            if (null !== $user->certificate) {
                $user->mun_id = $user->certificate->payers->mun;
                echo $user->save() ? 'OK' : 'Something wrong';
                echo PHP_EOL;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return false;
    }
}
