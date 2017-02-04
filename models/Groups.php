<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "groups".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property integer $program_id
 * @property string $name
 *
 * @property Organization $organization
 * @property Programs $program
 */
class Groups extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'datestart', 'datestop', 'program_id'], 'required'],
            [['organization_id', 'program_id', 'year_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['address', 'schedule'], 'string'],
            [['datestop', 'datestart'], 'safe'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            //['datestart', 'compare', 'compareAttribute' => 'datestop', 'operator' => '<'],
            ['datestop', 'compare', 'compareAttribute' => 'datestart', 'operator' => '>'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['year_id'], 'exist', 'skipOnError' => true, 'targetClass' => Years::className(), 'targetAttribute' => ['year_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Организация',
            'program_id' => 'Программа',
            'year_id' => 'ID Года',
            'name' => 'Название группы',
            'address' => 'Адрес',
            'schedule' => 'Расписание',
            'datestart' => 'Дата начала обучения',
            'datestop' => 'Дата окончания обучения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['id' => 'organization_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }
    
    public function getGroup($id)
    {
         $query = Groups::find();

        $query->where(['id' => $id]);

        return $query->one();
    }
    
        public function getYear()
    {
        return $this->hasOne(Years::className(), ['id' => 'year_id']);
    }
}
