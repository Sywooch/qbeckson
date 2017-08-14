<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "operators".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $OGRN
 * @property integer $INN
 * @property integer $KPP
 * @property integer $OKPO
 * @property string $address_legal
 * @property string $address_actual
 * @property string $phone
 * @property string $email
 * @property string $position
 * @property string $fio
 *
 * @property OperatorSettings $settings
 * @property User $user
 */
class Operators extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'operators';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'name', 'OGRN', 'INN', 'KPP', 'OKPO', 'address_legal', 'address_actual', 'phone', 'email', 'position', 'fio'], 'required'],
            [['user_id', 'OGRN', 'INN', 'KPP', 'OKPO', 'region'], 'integer'],
            [['name', 'address_legal', 'address_actual', 'phone', 'email', 'position', 'fio'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Наименование',
            'OGRN' => 'ОРГН',
            'INN' => 'ИНН',
            'KPP' => 'КПП',
            'OKPO' => 'ОКПО',
            'address_legal' => 'Адрес юридический',
            'address_actual' => 'Адрес фактический',
            'phone' => 'Телефон',
            'email' => 'Email',
            'position' => 'Должность ответственного лица',
            'fio' => 'ФИО ответственного лица',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettings()
    {
        return $this->hasOne(OperatorSettings::class, ['operator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @param string $region
     * @return bool
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this->save();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizations()
    {
        return $this->hasMany(Organization::class, ['id' => 'organization_id'])
            ->viaTable('organization_operator_assignment', ['operator_id' => 'id']);
    }

    /**
     * Use UserIdentity()->operator instead
     *
     * @deprecated
     */
    public function getOperators()
    {
        $query = self::find();
        if (!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }
}
