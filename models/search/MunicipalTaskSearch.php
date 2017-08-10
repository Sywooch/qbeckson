<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MunicipalTask;

/**
 * MunicipalTaskSearch represents the model behind the search form about `app\models\MunicipalTask`.
 */
class MunicipalTaskSearch extends MunicipalTask
{
    public $organization;
    public $municipality;
    public $hours;

    public $modelName;

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
            [['id', 'form', 'mun', 'ground', 'price', 'study', 'last_contracts',
                'last_s_contracts', 'last_s_contracts_rod', 'year', 'both_teachers', 'ovz', 'quality_control', 'p3z', 'municipality'], 'integer'],
            [['name', 'vid', 'colse_date', 'task', 'annotation', 'fullness', 'complexity', 'norm_providing',
                'zab', 'link', 'certification_date', 'verification', 'organization_id'], 'safe'],
            [['ocen_fact', 'ocen_kadr', 'ocen_mat', 'ocen_obch'], 'number'],
            [['organization', 'hours', 'age_group_min', 'age_group_max', 'limit', 'rating'], 'string'],
            ['open', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'age_group_min' => 'Возраст от',
            'age_group_max' => 'Возраст до',
            'hours' => 'Кол-во часов',
            'organization' => 'Название организации',
            'mun' => 'Муниципалитет'
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
     * @param array $params
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 50)
    {
        $query = Programs::find()->select([
            'programs.*',
            'SUM(years.hours) as countHours'
        ])
        ->joinWith([
            'municipality',
            'organization',
            'modules'
        ])
            ->andWhere('mun.operator_id = ' . Yii::$app->operator->identity->id);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => false,
                'pageSize' => $pageSize,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        if (isset($this->open) && $this->open < 1) {
            $query->andWhere(['OR', ['programs.open' => null], ['programs.open' => 0]]);
        }

        $query->andFilterWhere([
            'programs.id' => $this->id,
            'programs.organization_id' => $this->organization_id,
            'programs.verification' => $this->verification,
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
            'organization.mun' => $this->municipality,
        ]);

        $query->andFilterWhere(['like', 'programs.name', $this->name])
            ->andFilterWhere(['like', 'programs.directivity', $this->directivity])
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

        $query->groupBy(['programs.id']);

        return $dataProvider;
    }
}
