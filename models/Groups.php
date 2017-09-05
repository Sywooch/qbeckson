<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "groups".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property integer $program_id
 * @property integer $year_id
 * @property string $name
 * @property string $datestart
 * @property string $datestop
 * @property integer $status
 * @property boolean $isActive
 *
 * @property Organization $organization
 * @property Contracts[] $contracts
 * @property ProgrammeModule $module
 * @property mixed $year
 * @property Programs $program
 * @property GroupClass[] $classes
 */
class Groups extends ActiveRecord
{

    const STATUS_ARCHIVED = 0;
    const STATUS_ACTIVE   = 10;

    /**/
    public static function findActive()
    {
       return self::find()->where(['status' => self::STATUS_ACTIVE]);
    }

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
            [['organization_id', 'program_id', 'year_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['address', 'schedule'], 'string'],
            [['datestop', 'datestart'], 'safe'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            //['datestart', 'compare', 'compareAttribute' => 'datestop', 'operator' => '<'],
            ['datestop', 'compare', 'compareAttribute' => 'datestart', 'operator' => '>'],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['year_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProgrammeModule::className(), 'targetAttribute' => ['year_id' => 'id']],
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
            'status' => 'Статус',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClasses()
    {
        return $this->hasMany(GroupClass::class, ['group_id' => 'id']);
    }

    /**
     * @return string
     */
    public function formatClasses()
    {
        if ($this->classes) {
            $result = '';
            foreach ($this->classes as $class) {
                $result .= '<p>' .
                    $class->week_day . ': ' . $class->time_from . ' - ' . $class->time_to . ' ' . $class->address .
                    '</p>';
            }

            return $result;
        }

        return '<p>Адрес: ' . $this->address . '</p><p>' . 'Расписание: ' . $this->schedule . '</p>';
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(ProgrammeModule::className(), ['id' => 'year_id']);
    }

    /**
     * Все контракты
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(Contracts::class, ['group_id' => 'id']);
    }

    /**
     * Контракты в работе
     * @return \yii\db\ActiveQuery
     */
    public function getLivingContracts()
    {
          return $this->getContracts()->andFilterWhere(['status' => [Contracts::STATUS_CREATED,
              Contracts::STATUS_ACTIVE,
              Contracts::STATUS_ACCEPTED]]);
    }

    /**
     * @deprecated
     * @param $id
     * @return array|null|ActiveRecord
     */
    public function getGroup($id)
    {
        $query = Groups::find()
            ->where(['id' => $id]);

        return $query->one();
    }

    /**
     * @deprecated
     * @return \yii\db\ActiveQuery
     */
    public function getYear()
    {
        return $this->hasOne(ProgrammeModule::className(), ['id' => 'year_id']);
    }

    public function getIsActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * можно отправить в архив?
     * @return boolean
     */
    public function canBeArchived()
    {
        return !$this->getLivingContracts()->exists();
    }

    /**
     * установка флага "в архиве"
     * @return boolean
     */
    public function archive()
    {
        if(!$this->canBeArchived()){
            return false;
        }
        $this->status = self::STATUS_ARCHIVED;
        return $this->save(false);
    }
}
