<?php

namespace app\models;

use Yii;
use app\models\Programs;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "years".
 *
 * @property integer $id
 * @property integer $program_id
 * @property integer $year
 * @property integer $month
 * @property integer $previus
 * @property integer $open
 *
 * @property Programs $program
 * @property Contracts[] $activeContracts
 * @property ProgramModuleAddress $mainAddress
 * @property ProgramModuleAddress[] $addresses
 */
class ProgrammeModule extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'years';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = $scenarios['default'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['month', 'hours', 'hoursindivid', 'hoursdop', 'kvfirst', 'kvdop'], 'required'],
            [['name', 'minchild', 'maxchild', 'results'], 'required', 'on' => self::SCENARIO_CREATE],
            [['hours', 'program_id', 'year', 'hoursdop', 'hoursindivid', 'minchild', 'maxchild', 'open', 'quality_control', 'p21z', 'p22z'], 'integer'],
            [['price', 'normative_price'], 'number'],
            [['month'], 'integer', 'max' => 12],
            [['kvfirst', 'kvdop', 'name'], 'string', 'max' => 255],
            ['results', 'string'],
            [['minchild', 'maxchild'], 'integer', 'min' => 1],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(), 'targetAttribute' => ['program_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование модуля',
            'program_id' => 'Program ID',
            'year' => 'Модуль',
            'month' => 'Число месяцев реализации',
            'hours' => 'Продолжительность реализации образовательной программы в часах',
            'kvfirst' => 'Квалификация педагогического работника непосредственно осуществляющего реализацию образовательной программы в группе детей',
            'hoursindivid' => 'Число часов работы педагогического работника, предусмотренное на индивидуальное сопровождение детей',
            'hoursdop' => 'Число часов сопровождения группы дополнительным педагогическим работником одновременно с педагогическим работником, непосредственно осуществляющим реализацию образовательной программы',
            'kvdop' => 'Квалификация педагогического работника, дополнительно привлекаемого для совместной реализации образовательной программы в группе',
            'minchild' => 'Ожидаемое минимальное число детей, обучающееся в одной группе',
            'maxchild' => 'Ожидаемое максимальное число детей, обучающееся в одной группе',
            'price' => 'Цена программы',
            'normative_price' => 'Нормативная стоимость',
            'open' => 'Зачисление',
            'previus' => 'Предварительная запись',
            'quality_control' => 'Число оценок качества',
            'p21z' => 'Квалификация педагогического работника непосредственно осуществляющего реализацию образовательной программы в группе детей',
            'p22z' => 'Квалификация педагогического работника, дополнительно привлекаемого для совместной реализации образовательной программы в группе',
            'results' => 'Ожидаемые результаты освоения модуля',
            'fullname' => 'Наименование модуля',
        ];
    }

    public function getModuleAddressAssignments()
    {
        return $this->hasMany(ProgramModuleAddressAssignment::class, ['program_module_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramAddressesAssignments()
    {
        return $this->hasMany(ProgramAddressAssignment::class, ['id' => 'program_id'])
            ->via('moduleAddressAssignments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(OrganizationAddress::class, ['id' => 'organization_address_id'])
            ->via('programAddressesAssignments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainAddress()
    {

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveContracts()
    {
        return $this->hasMany(Contracts::class, ['year_id' => 'id'])
            ->andWhere(['contracts.status' => 1]);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Programs::className(), ['id' => 'program_id']);
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return 'Модуль ' . $this->year;
    }

    /**
     * @param bool $prefix
     * @return string
     */
    public function getFullname($prefix = true)
    {
        return ($prefix === false ?: 'Модуль ' . $this->year . (empty($this->name) ? '' : '. ') . $this->name);
    }

    public function getOpenYear()
    {
        $programs = new Programs();
        $program = $programs->getCooperateProgram();
        if (empty($program)) {
            $program = 0;
        }

        $rows = (new \yii\db\Query())
            ->select(['program_id'])
            ->from('years')
            ->where(['open' => 1])
            ->andWhere(['program_id' => $program])
            ->column();

        return array_unique($rows);
    }

    public function getAllYear()
    {
        $programs = new Programs();
        $program = $programs->getCooperateProgram();
        if (empty($program)) {
            $program = 0;
        }

        $rows = (new \yii\db\Query())
            ->select(['program_id'])
            ->from('years')
            ->andWhere(['program_id' => $program])
            ->column();

        return array_unique($rows);
    }
}
