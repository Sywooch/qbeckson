<?php

namespace app\models\siteRestriction;

use yii\db\ActiveRecord;

/**
 * запрет доступа к сайту
 *
 * @property int $id [int(11)]
 * @property string $type [varchar(50)]  тип запрета доступа к сайту {@see SiteRestrictionType}
 * @property string $message [varchar(255)]  сообщение причины запрета
 * @property bool $status [tinyint(1)]  статус запрета {@see SiteRestrictionStatus}
 */
class SiteRestriction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'site_restriction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'message', 'status'], 'required'],
            ['type', 'in', 'range' => SiteRestrictionType::getList()],
            ['message', 'string'],
            ['status', 'in', 'range' => SiteRestrictionStatus::getList()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Действие запрета',
            'message' => 'Сообщение причины запрета',
            'status' => 'Статус запрета',
        ];
    }
}