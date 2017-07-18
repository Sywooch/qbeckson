<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Programs;

/**
 * ProgramsSearch represents the model behind the search form about `app\models\Programs`.
 */
class ProgramsSearch extends Programs
{
    public $organization;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'form', 'mun', 'ground', 'price', 'limit', 'study', 'last_contracts', 'last_s_contracts', 'last_s_contracts_rod', 'year', 'both_teachers', 'ovz', 'age_group_min', 'age_group_max', 'quality_control', 'p3z'], 'integer'],
            [['name', 'vid', 'colse_date', 'task', 'annotation', 'fullness', 'complexity', 'norm_providing', 'zab', 'link', 'certification_date', 'verification'], 'safe'],
            [['rating', 'ocen_fact', 'ocen_kadr', 'ocen_mat', 'ocen_obch'], 'number'],
            ['organization', 'string'],
            ['open', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Programs::find()
            ->joinWith(['municipality'])
            ->where('`mun`.operator_id = ' . Yii::$app->operator->identity->id)
            ->joinWith('organization');

        $sort = new \yii\data\Sort([
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'attributes' => [
                'id',
                'organization' => [
                    'asc' => ['organization.name' => SORT_ASC],
                    'desc' => ['organization.name' => SORT_DESC],
                ],
            ],
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
            'sort' => $sort,
        ]);

        $this->load($params);
        //print_r($dataProvider->sort->getAttributeOrders());exit;

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        // grid filtering conditions
        if (isset($this->open) && $this->open < 1) {
            $query->andWhere(['or', ['open' => null], ['open' => 0]]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'verification' => $this->verification,
            'form' => $this->form,
            'mun' => $this->mun,
            'ground' => $this->ground,
            'price' => $this->price,
            'rating' => $this->rating,
            'limit' => $this->limit,
            'study' => $this->study,
            'last_contracts' => $this->last_contracts,
            'last_s_contracts' => $this->last_s_contracts,
            'last_s_contracts_rod' => $this->last_s_contracts_rod,
            'colse_date' => $this->colse_date,
            'year' => $this->year,
            'both_teachers' => $this->both_teachers,
            'ovz' => $this->ovz,
            'age_group_min' => $this->age_group_min,
            'age_group_max' => $this->age_group_max,
            'quality_control' => $this->quality_control,
            'certification_date' => $this->certification_date,
            'p3z' => $this->p3z,
            'ocen_fact' => $this->ocen_fact,
            'ocen_kadr' => $this->ocen_kadr,
            'ocen_mat' => $this->ocen_mat,
            'ocen_obch' => $this->ocen_obch,
        ]);

        $query->andFilterWhere(['like', '`programs`.name', $this->name])
            ->andFilterWhere(['like', 'directivity', $this->directivity])
            ->andFilterWhere(['like', 'vid', $this->vid])
            ->andFilterWhere(['like', 'task', $this->task])
            ->andFilterWhere(['like', 'annotation', $this->annotation])
            ->andFilterWhere(['like', 'fullness', $this->fullness])
            ->andFilterWhere(['like', 'complexity', $this->complexity])
            ->andFilterWhere(['like', 'norm_providing', $this->norm_providing])
            ->andFilterWhere(['like', 'zab', $this->zab])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'organization.name', $this->organization]);

        return $dataProvider;
    }
}
