<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Programs;
use app\models\Organization;

/**
 * ProgramsSearch represents the model behind the search form about `app\models\Programs`.
 */
class ProgramsallSearch extends Programs
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'verification', 'rating', 'limit', 'study', 'open', 'ovz', 'quality_control'], 'integer'],
            [['name', 'task', 'annotation', 'link', 'vid', 'certification_date'], 'safe'],
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
            ->where('mun.operator_id = ' . Yii::$app->operator->identity->id);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $payers = new Payers();
        $payer = $payers->getPayer();
        
        $organizations = new Organization();
        $organization = $organizations->getActualOrganization();
        
        $years = new ProgrammeModule();
        $year = $years->getOpenYear();
        if (empty($year)) { $year = 0; }

        // grid filtering conditions
        $query->andFilterWhere([
            'programs.id' => $year,
            'programs.organization_id' => $organization,
            'programs.verification' => 2,
            'programs.rating' => $this->rating,
            'programs.limit' => $this->limit,
            'programs.study' => $this->study,
            'programs.ovz' => $this->ovz,
            'programs.quality_control' => $this->quality_control,
            'programs.certification_date' => $this->certification_date,
        ]);

        $query->andFilterWhere(['like', 'programs.name', $this->name])
            ->andFilterWhere(['like', 'programs.direction_id', $this->direction_id])
            ->andFilterWhere(['like', 'programs.task', $this->task])
            ->andFilterWhere(['like', 'programs.annotation', $this->annotation])
            ->andFilterWhere(['like', 'programs.link', $this->link])
            ->andFilterWhere(['like', 'programs.vid', $this->vid]);
        

        return $dataProvider;
    }
}
