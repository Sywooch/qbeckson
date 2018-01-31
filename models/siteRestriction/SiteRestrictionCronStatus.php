<?php

namespace app\models\siteRestriction;

use yii\db\ActiveRecord;

/**
 * статус работы крона для запрета доступа к сайту
 *
 * @property bool $active [tinyint(1)]  активен ли крон
 */
class SiteRestrictionCronStatus extends ActiveRecord
{
    /** @inheritdoc */
    public static function tableName()
    {
        return 'site_restriction_cron_status';
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['active', 'required'],
            ['active', 'boolean'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'active' => 'активен ли крон',
        ];
    }

    /**
     * активен ли крон
     *
     * @return boolean
     */
    public static function isActive()
    {
        $self = self::find()->one();

        if (!$self) {
            $self = new self();
            $self->active = false;
            $self->save();
        }

        return $self->active;
    }

    /**
     * активировать крон
     */
    public static function activate()
    {
        $self = self::find()->one();

        if (!$self) {
            $self = new self();
        }

        $self->active = true;
        $self->save();
    }

    /**
     * дезактивировать крон
     */
    public static function deactivate()
    {
        $self = self::find()->one();

        if (!$self) {
            $self = new self();
        }

        $self->active = false;
        $self->save();
    }

}