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
class ProgramsPreviusSearch extends Programs
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
            ->andWhere('is_municipal_task < 1');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
        $year = $years->getAllYear();
        if (empty($year)) {
            $year = 0;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $year,
            'organization_id' => $organization,
            'verification' => 2,
            'rating' => $this->rating,
            'limit' => $this->limit,
            'study' => $this->study,
            //'open' => 1,
            'ovz' => $this->ovz,
            'quality_control' => $this->quality_control,
            'certification_date' => $this->certification_date,
        ]);

        $query->andFilterWhere(['like', '`programs`.name', $this->name])
            ->andFilterWhere(['like', 'directivity', $this->directivity])
            ->andFilterWhere(['like', 'task', $this->task])
            ->andFilterWhere(['like', 'annotation', $this->annotation])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['like', 'vid', $this->vid]);


        return $dataProvider;
    }
}
