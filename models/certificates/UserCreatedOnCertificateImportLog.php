<?php

namespace app\models\certificates;

use app\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * лог созданных пользователей при импорте списка сертификатов
 *
 * @property int $id [int(11)]
 * @property int $user_id [int(11)]  id созданного пользователя
 * @property string $created_at [datetime]  дата и время создания пользователя
 */
class UserCreatedOnCertificateImportLog extends ActiveRecord
{
    /** @inheritdoc */
    public static function tableName()
    {
        return 'user_created_on_certificate_import_log';
    }
    
    /** @inheritdoc */
    public function rules()
    {
        return [
            ['user_id', 'exist', 'targetClass' => User::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
                'value' => date('Y-m-d H:i:s', time()),
            ]
        ];
    }
}