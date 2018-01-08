<?php

namespace app\models\contracts;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * лог автопролонгированных контрактов
 *
 * @property int $id [int(11)]
 * @property int $organization_id [int(11)]  id организации проводившей автопролонгацию
 * @property int $contract_parent_id [int(11)]  id родительского контракта
 * @property int $contract_child_id [int(11)]  id дочернего контракта
 * @property int $group_id [int(11)]  если автопролонгация проходила в новую группу id группы, иначе null
 * @property string $auto_prolonged_at [datetime]  дата и время автопролонгации контракта
 */
class ContractAutoProlongedLog extends ActiveRecord
{
    /** @inheritdoc */
    public static function tableName()
    {
        return 'contract_auto_prolonged_log';
    }
    
    /** @inheritdoc */
    public function rules()
    {
        return [
            [['organization_id', 'contract_parent_id', 'contract_child_id', 'group_id'], 'integer'],
            ['auto_prolonged_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
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
                'createdAtAttribute' => 'auto_prolonged_at',
                'updatedAtAttribute' => null,
                'value' => date('Y-m-d H:i:s'),
            ]
        ];
    }
}