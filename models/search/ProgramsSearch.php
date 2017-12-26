<?php

namespace app\models\search;

use app\components\ActiveDataProviderWithDecorator;
use app\models\Cooperate;
use app\models\OrganizationPayerAssignment;
use app\models\Payers;
use app\models\ProgrammeModule;
use app\models\Programs;
use app\models\UserIdentity;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * ProgramsSearch represents the model behind the search form about `app\models\Programs`.
 */
class ProgramsSearch extends Programs
{

    const MODEL_WAIT = 'SearchWaitPrograms';
    const MODEL_OPEN = 'SearchOpenPrograms';
    const MODEL_CLOSED = 'SearchClosedPrograms';


    public $organization;
    public $municipality;
    public $hours;
    public $isMunicipalTask = false;
    public $age;

    public $modelName;
    public $payerId;
    public $taskPayerId;
    public $idList;

    public $decorator;

    /**
     * @return string
     */
    public function formName()
    {
        return $this->modelName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'form', 'mun', 'ground', 'price', 'study', 'last_contracts', 'direction_id',
                'last_s_contracts', 'last_s_contracts_rod', 'year', 'both_teachers', 'ovz', 'quality_control', 'p3z', 'municipality', 'age', 'municipal_task_matrix_id'], 'integer'],
            [['name', 'vid', 'colse_date', 'task', 'annotation', 'fullness', 'complexity', 'norm_providing',
                'zab', 'link', 'certification_date', 'verification', 'organization_id', 'payerId', 'activity_ids', 'taskPayerId'], 'safe'],
            [['ocen_fact', 'ocen_kadr', 'ocen_mat', 'ocen_obch'], 'number'],
            [['organization', 'hours', 'age_group_min', 'age_group_max', 'limit', 'rating'], 'string'],
            ['open', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'age_group_min' => 'Возраст от',
            'age_group_max' => 'Возраст до',
            'hours' => 'Кол-во часов',
            'organization' => 'Название организации',
            'mun' => 'Муниципалитет',
            'normativePrice' => 'НС*',
            'price' => 'Цена*',
            'age' => 'Возраст'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param int $pageSize
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 50)
    {
        $query = Programs::find()
            ->select([
                'programs.*',
                'SUM(years.hours) as countHours'
            ])
            ->joinWith([
                'municipality',
                'organization',
                'modules',
                'activities'
            ]);

        $query->leftJoin(Payers::tableName(), 'programs.mun = payers.mun');


        $query->andWhere('mun.operator_id = ' . Yii::$app->operator->identity->id);

        if ($this->isMunicipalTask) {
            $query->andWhere(['>', 'programs.is_municipal_task', 0]);
        } else {
            if (Yii::$app->user->can(UserIdentity::ROLE_PAYER)
            ) {
                $query
                    ->leftJoin(
                        Cooperate::tableName(),
                        'cooperate.organization_id = programs.organization_id
                        and cooperate.payer_id = payers.id'
                    )
                    ->andWhere([
                        'cooperate.status' => Cooperate::STATUS_ACTIVE,
                        'cooperate.period' => [Cooperate::PERIOD_CURRENT, Cooperate::PERIOD_FUTURE]
                    ]);
            }

            $query->andWhere([
                'OR',
                ['programs.is_municipal_task' => null],
                ['programs.is_municipal_task' => 0]
            ]);
        }

        if ($this->decorator) {
            $dataProvider = new ActiveDataProviderWithDecorator([
                'decoratorClass' => $this->decorator,
                'query' => $query,
                'pagination' => [
                    'pageSizeLimit' => false,
                    'pageSize' => $pageSize,
                ]
            ]);
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSizeLimit' => false,
                    'pageSize' => $pageSize,
                ]
            ]);
        }

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        if ($this->payerId) {
            /** @var UserIdentity $user */
            $user = Yii::$app->user->getIdentity();
            $organizationIds = $user->payer->getOrganizationIdListWithCurrentOrFutureCooperate();
            if ($this->organization_id && $organizationIds && $this->organization_id !== 'Array') {
                $this->organization_id = ArrayHelper::isIn($this->organization_id, $organizationIds) ?
                    $this->organization_id : 0;
            } else {
                $this->organization_id = $organizationIds ?: 0;
            }
        }

        if ($this->taskPayerId) {
            /** @var UserIdentity $user */
            $payer = Payers::findOne($this->taskPayerId);
            $organizationIds = ArrayHelper::getColumn($payer->getOrganizations(null, OrganizationPayerAssignment::STATUS_ACTIVE)->all(), 'id');
            if ($this->organization_id && $organizationIds && $this->organization_id !== 'Array') {
                $this->organization_id = ArrayHelper::isIn($this->organization_id, $organizationIds) ?
                    $this->organization_id : 0;
            } else {
                $this->organization_id = $organizationIds ?: 0;
            }
        }

        if (isset($this->open) && $this->open < 1) {
            $query->andWhere(['OR', ['programs.open' => null], ['programs.open' => 0]]);
        }

        $query->andFilterWhere([
            'programs.id' => $this->id,
            'programs.organization_id' => $this->organization_id,

            'programs.form' => $this->form,
            'programs.mun' => $this->mun,
            'programs.ground' => $this->ground,
            'programs.price' => $this->price,
            'programs.study' => $this->study,
            'programs.last_contracts' => $this->last_contracts,
            'programs.last_s_contracts' => $this->last_s_contracts,
            'programs.last_s_contracts_rod' => $this->last_s_contracts_rod,
            'programs.colse_date' => $this->colse_date,
            'programs.year' => $this->year,
            'programs.both_teachers' => $this->both_teachers,
            'programs.ovz' => $this->ovz,
            'programs.quality_control' => $this->quality_control,
            'programs.certification_date' => $this->certification_date,
            'programs.p3z' => $this->p3z,
            'programs.ocen_fact' => $this->ocen_fact,
            'programs.ocen_kadr' => $this->ocen_kadr,
            'programs.ocen_mat' => $this->ocen_mat,
            'programs.ocen_obch' => $this->ocen_obch,
            'programs.direction_id' => $this->direction_id,
            'programs.municipal_task_matrix_id' => $this->municipal_task_matrix_id,
            'organization.mun' => $this->municipality,
        ]);
        if ($this->formName() === self::MODEL_WAIT) {
            $query->andFilterWhere(
                [
                    'or',
                    ['programs.verification' => $this->verification,],
                    [
                        'and',
                        [
                            'programs.verification' => Programs::VERIFICATION_DONE,
                        ],
                        [
                            '{{%years}}.verification' => [
                                ProgrammeModule::VERIFICATION_UNDEFINED,
                                ProgrammeModule::VERIFICATION_WAIT
                            ]
                        ]
                    ]]
            );
        } else {
            $query->andFilterWhere(['programs.verification' => $this->verification,]);
        }
        $query->andFilterWhere(['<=', 'programs.age_group_min', $this->age]);
        $query->andFilterWhere(['>=', 'programs.age_group_max', $this->age]);

        $query->andFilterWhere(['like', 'programs.name', $this->name])
            ->andFilterWhere(['like', 'programs.vid', $this->vid])
            ->andFilterWhere(['like', 'programs.task', $this->task])
            ->andFilterWhere(['like', 'programs.annotation', $this->annotation])
            ->andFilterWhere(['like', 'programs.fullness', $this->fullness])
            ->andFilterWhere(['like', 'programs.complexity', $this->complexity])
            ->andFilterWhere(['like', 'programs.norm_providing', $this->norm_providing])
            ->andFilterWhere(['like', 'programs.link', $this->link])
            ->andFilterWhere(['like', 'organization.name', $this->organization]);

        if (!empty($this->zab)) {
            $query->andFilterWhere(['or like', 'programs.zab', $this->zab]);
        }

        if ($this->activity_ids) {
            $query->andWhere(array_reduce($this->activity_ids, function ($acc, $value) {
                    $acc[] = ['directory_program_activity.id' => $value];

                    return $acc;
                }, ['OR'])
            );
        }

        if (!empty($this->age_group_min) && empty($this->age_group_max)) {
            $query->andWhere([
                'OR',
                ['>=', 'programs.age_group_min', $this->age_group_min],
                ['>=', 'programs.age_group_max', $this->age_group_min]
            ]);
        }

        if (!empty($this->age_group_max) && empty($this->age_group_min)) {
            $query->andWhere([
                'OR',
                ['<=', 'programs.age_group_min', $this->age_group_max],
                ['<=', 'programs.age_group_max', $this->age_group_max]
            ]);
        }

        if (!empty($this->age_group_min) && !empty($this->age_group_max)) {
            $query->andWhere([
                'OR',
                [
                    'AND',
                    ['>=', 'programs.age_group_min', $this->age_group_min],
                    ['<=', 'programs.age_group_min', $this->age_group_max]
                ],
                [
                    'AND',
                    ['>=', 'programs.age_group_max', $this->age_group_min],
                    ['<=', 'programs.age_group_max', $this->age_group_max]
                ]
            ]);
        }

        if (!empty($this->hours) && $this->hours !== '0,2000') {
            $hours = explode(',', $this->hours);
            $query->andHaving([
                'AND',
                ['>=', 'countHours', (int)$hours[0]],
                ['<=', 'countHours', (int)$hours[1]]
            ]);
        }

        if (!empty($this->rating) && $this->rating !== '0,100') {
            $rating = explode(',', $this->rating);
            $query->andWhere([
                'AND',
                ['>=', 'programs.rating', (int)$rating[0]],
                ['<=', 'programs.rating', (int)$rating[1]]
            ]);
        }

        if (!empty($this->limit) && $this->limit !== '0,10000') {
            $limit = explode(',', $this->limit);
            $query->andWhere([
                'AND',
                ['>=', 'programs.limit', (int)$limit[0]],
                ['<=', 'programs.limit', (int)$limit[1]]
            ]);
        }

        $query->andFilterWhere(['programs.id' => $this->idList]);

        $query->groupBy(['programs.id']);

        return $dataProvider;
    }
}
