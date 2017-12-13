<?php

namespace app\models;

use app\components\periodicField\PeriodicField;
use app\components\periodicField\PeriodicFieldAR;
use app\components\periodicField\PeriodicFieldBehavior;
use app\components\periodicField\RecordWithHistory;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "years".
 *
 * @property integer $id
 * @property string $name
 * @property integer $program_id
 * @property integer $year  порядковый номер модуля
 * @property integer $month Число месяцев реализации
 * @property integer $hours
 * @property string $kvfirst  Квалификация пед работника
 * @property string $kvdop    Квалификация дополнительно пед работника
 * @property integer $hoursindivid
 * @property integer $hoursdop
 * @property integer $maxchild
 * @property integer $minchild
 * @property float $price
 * @property float $normative_price
 * @property integer $rating
 * @property integer $limits
 * @property integer $open
 * @property integer $previus
 * @property integer $quality_control
 * @property integer $p21z
 * @property integer $p22z
 * @property string $results
 * @property string $fullname
 * @property integer $verification
 *
 * @property Programs $program
 * @property Contracts[] $activeContracts
 * @property OrganizationAddress[] $addresses
 * @property ProgramModuleAddress[] $oldAddresses
 * @property OrganizationAddress $mainAddress
 * @property ProgramModuleAddressAssignment[] $moduleAddressAssignments
 * @property Groups[] $groups
 */
class ProgrammeModule extends ActiveRecord implements RecordWithHistory
{
    use PeriodicField;

    const VERIFICATION_UNDEFINED = 0;
    const VERIFICATION_WAIT = 1;
    const VERIFICATION_DONE = 2;
    const VERIFICATION_DENIED = 3;
    const VERIFICATION_DRAFT = 5;
    const VERIFICATION_IN_ARCHIVE = 10;

    const SCENARIO_CREATE = 'create';

    const SCENARIO_MUNICIPAL_TASK = 'municipal-task';

    public $edit;

    public function fieldResolver(PeriodicFieldAR $history)
    {
        if ($history->field_name === 'verification') {
            switch ($history->value) {
                case self::VERIFICATION_UNDEFINED:
                    return 'не определенная';
                case self::VERIFICATION_WAIT:
                    return 'Ожидает';
                case self::VERIFICATION_DONE:
                    return 'Верифицированно успешно';
                case self::VERIFICATION_DENIED:
                    return 'Отказ';
                case self::VERIFICATION_DRAFT:
                    return 'Черновик';
                case self::VERIFICATION_IN_ARCHIVE:
                    return 'Программа в архиве';
                default:
                    return 'не известное значение: ' . $history->value;
            }
        } elseif ($history->field_name === 'open') {
            return $history->value ? 'открыто' : 'закрыто';
        } else {
            return $history->value;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%years}}';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = $scenarios['default'];
        $scenarios[self::SCENARIO_MUNICIPAL_TASK] = $scenarios[self::SCENARIO_CREATE];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['month', 'hours', 'hoursindivid', 'hoursdop', 'kvfirst', 'kvdop'], 'required', 'on' => 'default'],
            [
                ['month', 'hours', 'hoursindivid', 'hoursdop', 'kvfirst', 'kvdop'],
                'required', 'on' => self::SCENARIO_CREATE
            ],
            [['month', 'hours', 'kvfirst'], 'required', 'on' => self::SCENARIO_MUNICIPAL_TASK],
            [['name', 'minchild', 'maxchild', 'results'], 'required', 'on' => self::SCENARIO_CREATE],
            [['name', 'minchild', 'maxchild', 'results'], 'required', 'on' => self::SCENARIO_MUNICIPAL_TASK],
            [
                [
                    'hours', 'program_id', 'year', 'hoursdop', 'hoursindivid', 'minchild',
                    'maxchild', 'open', 'quality_control', 'p21z', 'p22z'
                ],
                'integer'
            ],
            [['price', 'normative_price'], 'number'],
            [['verification'], 'integer'],
            [['month'], 'integer', 'max' => 12],
            [['kvfirst', 'kvdop', 'name'], 'string', 'max' => 255],
            ['results', 'string'],
            [['minchild', 'maxchild'], 'integer', 'min' => 1],
            [
                ['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programs::className(),
                'targetAttribute' => ['program_id' => 'id']
            ],
        ];
    }

    public function behaviors()
    {
        return [
            PeriodicFieldBehavior::className()
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
            'kvfirst' => 'Сведения о необходимой квалификации педагогического работника для реализации программы',
            'hoursindivid' => 'Число часов работы педагогического работника, предусмотренное на '
                . 'индивидуальное сопровождение детей',
            'hoursdop' => 'Число часов сопровождения группы дополнительным педагогическим работником '
                . 'одновременно с педагогическим работником, непосредственно осуществляющим реализацию '
                . 'образовательной программы',
            'kvdop' => 'Квалификация педагогического работника, дополнительно привлекаемого '
                . 'для совместной реализации образовательной программы в группе',
            'minchild' => 'Ожидаемое минимальное число детей, обучающееся в одной группе',
            'maxchild' => 'Ожидаемое максимальное число детей, обучающееся в одной группе',
            'price' => 'Стоимость модуля',
            'normative_price' => 'Нормативная стоимость',
            'open' => 'Зачисление',
            'previus' => 'Предварительная запись',
            'quality_control' => 'Число оценок качества',
            'p21z' => 'Квалификация педагогического работника непосредственно осуществляющего реализацию '
                . 'образовательной программы в группе детей',
            'p22z' => 'Квалификация педагогического работника, дополнительно привлекаемого для совместной '
                . 'реализации образовательной программы в группе',
            'results' => 'Ожидаемые результаты освоения модуля',
            'fullname' => 'Наименование модуля',
            'edit' => 'Отправить на повторную сертификацию',
            'verification' => 'Сертификация',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Groups::class, ['year_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(Contracts::class, ['group_id' => 'id'])
            ->via('groups');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModuleAddressAssignments()
    {
        return $this->hasMany(ProgramModuleAddressAssignment::class, ['program_module_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgramAddressesAssignments()
    {
        return $this->hasMany(ProgramAddressAssignment::class, ['id' => 'program_address_assignment_id'])
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
    public function getMainModuleAddressAssignments()
    {
        return $this->hasOne(ProgramModuleAddressAssignment::class, ['program_module_id' => 'id'])
            ->andWhere(['program_module_address_assignment.status' => 1]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainProgramAddressesAssignments()
    {
        return $this->hasOne(ProgramAddressAssignment::class, ['id' => 'program_address_assignment_id'])
            ->via('mainModuleAddressAssignments');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainAddress()
    {
        return $this->hasOne(OrganizationAddress::class, ['id' => 'organization_address_id'])
            ->via('mainProgramAddressesAssignments');
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveContracts()
    {
        return $this->hasMany(Contracts::class, ['year_id' => 'id'])
            ->andWhere(['contracts.status' => Contracts::STATUS_ACTIVE]);
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
     *
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

    public function canEdit()
    {
        $contractsExists = $this
            ->getContracts()
            ->andFilterWhere([
                'status' => [
                    Contracts::STATUS_REQUESTED,
                    Contracts::STATUS_ACTIVE,
                    Contracts::STATUS_ACCEPTED
                ]
            ])->exists();
        $isOpen = $this->open;

        return !($contractsExists || $isOpen);
    }

    public function needCertificate(): bool
    {
        return ($this->verification === self::VERIFICATION_UNDEFINED
                || $this->verification === self::VERIFICATION_WAIT)
            && ($this->program->verification !== Programs::VERIFICATION_UNDEFINED
                && $this->program->verification !== Programs::VERIFICATION_WAIT);
    }

    /**
     * @return integer|null
     */
    public function getChildrenAverage()
    {
        return $this->program->municipality->operator->settings->children_average;
    }

    public function setVerificationWaitAndSave(): bool
    {
        if ($this->verification === self::VERIFICATION_WAIT) {
            return true;
        }
        $this->verification = self::VERIFICATION_WAIT;

        return $this->save(false);
    }

    public function setNeedVerification(): self
    {
        if ($this->verification === self::VERIFICATION_WAIT
            || $this->verification === self::VERIFICATION_UNDEFINED
        ) {
            return $this;
        }
        $this->verification = self::VERIFICATION_UNDEFINED;

        return $this;
    }
}
