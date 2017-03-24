<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProgrammeModule;
use app\models\Programs;

/**
 * ProgrammeModuleSearch represents the model behind the search form about `app\models\ProgrammeModule`.
 */
class ProgrammeModuleNoSearch extends ProgrammeModule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'program_id', 'year', 'month', 'hours', 'hoursindivid', 'hoursdop', 'minchild', 'maxchild', 'price', 'normative_price', 'rating', 'limits', 'open', 'quality_control'], 'integer'],
            [['kvfirst', 'kvdop'], 'safe'],
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
        $query = ProgrammeModule::find();

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
        
        $programs = new Programs();
        $program = $programs->getOrganizationNoProgram();
        if (empty($program)) { $program = 0; }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'program_id' => $program,
            'year' => $this->year,
            'month' => $this->month,
            'hours' => $this->hours,
            'hoursindivid' => $this->hoursindivid,
            'hoursdop' => $this->hoursdop,
            'minchild' => $this->minchild,
            'maxchild' => $this->maxchild,
            'price' => $this->price,
            'normative_price' => $this->normative_price,
            'rating' => $this->rating,
            'limits' => $this->limits,
            'open' => $this->open,
            'quality_control' => $this->quality_control,
        ]);

        $query->andFilterWhere(['like', 'kvfirst', $this->kvfirst])
            ->andFilterWhere(['like', 'kvdop', $this->kvdop]);

        return $dataProvider;
    }
}
