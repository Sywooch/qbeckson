<?php

namespace app\models;

use app\models\statics\DirectoryProgramActivity;
use app\models\statics\DirectoryProgramDirection;
use voskobovich\linker\LinkerBehavior;
use Yii;
use app\models\Cooperate;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "programs".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property integer $verification
 * @property string $name
 * @property string $vid
 * @property integer $mun
 * @property integer $ground
 * @property integer $price
 * @property integer $normative_price
 * @property integer $rating
 * @property integer $limit
 * @property integer $study
 * @property integer $open
 * @property string $colse_date
 * @property string $task
 * @property string $annotation
 * @property integer $year
 * @property string $kvfirst
 * @property string $kvdop
 * @property integer $maxchild
 * @property integer $minchild
 * @property integer $both_teachers
 * @property string $fullness
 * @property string $complexity
 * @property string $norm_providing
 * @property integer $ovz
 * @property integer $zab
 * @property string $age_group
 * @property integer $quality_control
 * @property string $link
 * @property string $certification_date
 * @property array $activity_ids
 * @property integer $direction_id
 *
 * @property Contracts[] $contracts
 * @property Favorites[] $favorites
 * @property Groups[] $groups
 * @property Informs[] $informs
 * @property Organization $organization
 * @property ProgrammeModule[] $years
 * @property DirectoryProgramActivity[]|null $activities
 * @property DirectoryProgramDirection|null $direction
 * @property string $directivity
 * @property mixed $countMonths
 * @property mixed $organizationProgram
 * @property mixed $organizationWaitProgram
 * @property mixed $organizationNoProgram
 * @property Mun $municipality
 * @property mixed $cooperateProgram
 * @property mixed $countHours
 * @property string $commonActivities
 * @property ProgrammeModule[] $modules
 */
class Programs extends \yii\db\ActiveRecord
{
    public $file;
    public $edit;
    public $search;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'programs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['direction_id', 'name', 'task', 'annotation', 'ovz', 'norm_providing', 'age_group_min', 'age_group_max', 'ground'], 'required'],
            [['organization_id', 'ovz', 'mun', 'year', 'ground', 'age_group_min', 'age_group_max', 'verification', 'form', 'p3z', 'study', 'last_contracts', 'limit', 'last_s_contracts', 'quality_control', 'last_s_contracts_rod', 'direction_id'], 'integer'],
            [['rating', 'ocen_fact', 'ocen_kadr', 'ocen_mat', 'ocen_obch'], 'number'],
            [['task', 'annotation', 'vid', 'norm_providing', 'search'], 'string'],
            [['name', 'zab'], 'string', 'max' => 255],
            [['link'], 'string', 'max' => 45],
            [['organization_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'id']],
            ['age_group_min', 'compare', 'compareAttribute' => 'age_group_max', 'type' => 'number', 'operator' => '<='],
            ['age_group_max', 'compare', 'compareAttribute' => 'age_group_min', 'type' => 'number', 'operator' => '>='],

            [['activity_ids'], 'each', 'rule' => ['integer']],
            [
                'direction_id', 'exist', 'skipOnError' => true,
                'targetClass' => DirectoryProgramDirection::class,
                'targetAttribute' => ['direction_id' => 'id']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => LinkerBehavior::class,
                'relations' => [
                    'activity_ids' => 'activities',
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModules()
    {
        return $this->hasMany(ProgrammeModule::class, ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|DirectoryProgramActivity[]|null
     */
    public function getActivities()
    {
        return $this->hasMany(DirectoryProgramActivity::class, ['id' => 'activity_id'])
            ->viaTable('{{%program_activity_assignment}}', ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery|DirectoryProgramDirection|null
     */
    public function getDirection()
    {
        return $this->hasOne(DirectoryProgramDirection::className(), ['id' => 'direction_id']);
    }

    /**
     * @return string
     */
    // TODO: Избавиться от этого метода (старая система "направленностей")
    public function getDirectivity()
    {
        return $this->direction->old_name;
    }

    // TODO: Избавиться от этого метода (старая система "видов")
    public function getCommonActivities()
    {
        if (!empty($this->activities)) {
            return implode(', ', ArrayHelper::getColumn($this->activities, 'name'));
        }

        return $this->vid;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Организация',
            'verification' => 'Статус сертификации',
            'countHours' => 'Учебных часов',
            'form' => 'Форма обучения',
            'name' => 'Наименование программы',
            'directivity' => 'Направленность программы',
            'direction_id' => 'Направленность программы',
            'vid' => 'Вид деятельности образовательной программы',
            'commonActivities' => 'Вид деятельности образовательной программы',
            'activity_ids' => 'Виды деятельности образовательной программы',
            'mun' => 'Муниципальное образование',
            'annotation' => 'Аннотация программы',
            'task' => 'Цели и задачи программы',
            'age_group_min' => 'Возрастная категория детей, определяемая минимальным возрастом лиц, которые могут быть зачислены на обучение по образовательной программе',
            'age_group_max' => 'Возрастная категория детей, определяемая максимальным возрастом лиц, которые могут быть зачислены на обучение по образовательной программе',
            'ovz' => 'Категория состояния здоровья детей, которые могут быть зачислены на обучение по образовательной программе (ОВЗ/без ОВЗ)',
            'zab' => 'Заболевание',
            'year' => 'Число модулей',
            'norm_providing' => 'Нормы оснащения детей средствами обучения при проведении обучения по образовательной программе и интенсивность их использования',
            'ground' => 'Тип местности',
            'rating' => 'Рейтинг программы ',
            'limit' => 'Лимит зачисления',
            'link' => 'Ссылка на текст программы',
            'edit' => 'Отправить на повторную сертификацию',
            'p3z' => 'Коэффициент учета степени обеспечения оборудованием',
            //'price_next' => 'Ожидаемая стоимость будущего года',
            //'certification_date' => 'Дата направления программы на сертификацию',
            //'colse_date' => 'Дата завершения реализации программы',
            'study' => 'Число обучающихся',
            'last_contracts' => 'Число обучающихся и прошедших обучение',
            'last_s_contracts' => 'Прошедших обучение',
            'last_s_contracts_rod' => 'Прошедших обучение (расторгнутых родителем)',
            'quality_control' => 'Число оценок качества',
            //'both_teachers' => 'Число педагогических работников, одновременно реализующих программу',
            //'fullness' => 'Наполняемость группы при реализации программы',
            //'complexity' => 'Сложность оборудования и средств обучения используемых при реализации программы',
            'ocen_fact' => 'Оценка достижения заявленных результатов',
            'ocen_kadr' => 'Оценка выполнения кадровых требований',
            'ocen_mat' => 'Оценка выполнения требований к средствам обучения',
            'ocen_obch' => 'Оценка общей удовлетворенности программой',
            'selectyear' => 'Выберите год обучения по программе для просмотра подробной информации',
            'activities' => 'Виды деятельности',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(Contracts::className(), ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorites::className(), ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Groups::className(), ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInforms()
    {
        return $this->hasMany(Informs::className(), ['program_id' => 'id']);
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
    public function getMunicipality()
    {
        return $this->hasOne(Mun::className(), ['id' => 'mun']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getYears()
    {
        return $this->hasMany(ProgrammeModule::className(), ['program_id' => 'id']);
    }

    public static function getCountPrograms($organization_id = null, $verification = null)
    {
        $query = static::find()
            ->joinWith(['municipality'])
            ->where('`mun`.operator_id = ' . Yii::$app->operator->identity->id);

        if (!empty($organization_id)) {
            $query->andWhere(['organization_id' => $organization_id]);
        }
        if (!empty($verification)) {
            $query->andWhere(['verification' => $verification]);
        }

        return $query->count();
    }

    public function getPrograms($id)
    {

        $query = Programs::find();

        if (!Yii::$app->user->isGuest) {
            $query->where(['id' => $id]);
        }

        return $query->one();
    }

    public function getOrganizationProgram()
    {

        if (!Yii::$app->user->isGuest) {

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('programs')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['verification' => 2])
                ->column();

            return $rows;
        }
    }

    public function getOrganizationWaitProgram()
    {

        if (!Yii::$app->user->isGuest) {

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('programs')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['verification' => [0, 1]])
                ->column();

            return $rows;
        }
    }

    public function getOrganizationNoProgram()
    {

        if (!Yii::$app->user->isGuest) {

            $organizations = new Organization();
            $organization = $organizations->getOrganization();

            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('programs')
                ->where(['organization_id' => $organization['id']])
                ->andWhere(['verification' => 2])
                ->column();

            return $rows;
        }
    }

    public function getCooperateProgram()
    {

        if (!Yii::$app->user->isGuest) {

            $cooperates = new Cooperate();
            $cooperate = $cooperates->getCooperateOrg();
            if (empty($cooperate)) {
                $cooperate = 0;
            }


            $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('programs')
                ->where(['organization_id' => $cooperate])
                ->column();

            return array_unique($rows);
        }
    }

    public function munName($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['name'])
            ->from('mun')
            ->where(['id' => $data])
            ->one();

        return $rows['name'];
    }

    public function groundName($data)
    {

        return Yii::$app->params['ground'][$data];
    }

    public function otkazName($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['text'])
            ->from('informs')
            ->where(['program_id' => $data])
            ->andWhere(['status' => 3])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        return $rows['text'];
    }

    public function countContract($data)
    {

        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['program_id' => $data])
            ->andWhere(['status' => 1])
            ->count();

        return $rows;
    }

    public function countContractPayer($data, $id)
    {

        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['program_id' => $data])
            ->andWhere(['status' => 1])
            ->andWhere(['payer_id' => $id])
            ->count();

        return $rows;
    }

    public function zabName($data, $ovz)
    {
        if ($ovz == 2) {
            $zab = explode(',', $data);
            $display = '';
            foreach ($zab as $value) {
                if ($value == 1) {
                    $display = $display . ', глухие';
                }
                if ($value == 2) {
                    $display = $display . ', слабослышащие и позднооглохшие';
                }
                if ($value == 3) {
                    $display = $display . ', слепые';
                }
                if ($value == 4) {
                    $display = $display . ', слабовидящие';
                }
                if ($value == 5) {
                    $display = $display . ', нарушения речи';
                }
                if ($value == 6) {
                    $display = $display . ', фонетико-фонематическое нарушение речи';
                }
                if ($value == 7) {
                    $display = $display . ', нарушение опорно-двигательного аппарата';
                }
                if ($value == 8) {
                    $display = $display . ', задержка психического развития';
                }
                if ($value == 9) {
                    $display = $display . ', расстройство аутистического спектра';
                }
                if ($value == 10) {
                    $display = $display . ', нарушение интеллекта';
                }
            }
            if ($display == '') {
                return 'без ОВЗ';
            } else {
                return mb_substr($display, 2);
            }
        } else {
            return 'без ОВЗ';
        }
    }

    public function yearName($data)
    {
        if ($data == 1) {
            return 'Однолетняя';
        }
        if ($data == 2) {
            return 'Двухлетняя';
        }
        if ($data == 3) {
            return 'Трехлетняя';
        }
        if ($data == 4) {
            return 'Четырехлетняя';
        }
        if ($data == 5) {
            return 'Пятилетняя';
        }
        if ($data == 6) {
            return 'Шестилетняя';
        }
        if ($data == 7) {
            return 'Семилетняя';
        }
    }

    // TODO Избавиться от этого метода, джойнить программы с годами сразу
    public function getCountHours()
    {
        $query = "SELECT sum(`years`.hours) as summa FROM `programs` CROSS JOIN `years` ON `programs`.id = `years`.program_id WHERE `programs`.id=:id GROUP BY `programs`.id";

        $command = Yii::$app->db->createCommand($query, [':id' => $this->id]);
        $result = $command->queryOne();

        return $result['summa'];
    }

    // TODO Избавиться от этого метода, джойнить программы с годами сразу
    public function getCountMonths()
    {
        $query = "SELECT sum(`years`.`month`) as summa FROM `programs` CROSS JOIN `years` ON `programs`.id = `years`.program_id WHERE `programs`.id=:id GROUP BY `programs`.id";

        $command = Yii::$app->db->createCommand($query, [':id' => $this->id]);
        $result = $command->queryOne();

        return $result['summa'];
    }

    /**
     * @return array
     */
    public static function illnesses()
    {
        return [
            1 => 'глухие',
            2 => 'слабослышащие и позднооглохшие',
            3 => 'слепые',
            4 => 'слабовидящие',
            5 => 'нарушения речи',
            6 => 'фонетико-фонематическое нарушение речи',
            7 => 'нарушение опорно-двигательного аппарата',
            8 => 'задержка психического развития',
            9 => 'расстройство аутистического спектра',
            10 => 'нарушение интеллекта',
        ];
    }
}
