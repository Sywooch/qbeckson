<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Completeness;

/**
 * CompletenessSearch represents the model behind the search form about `app\models\Completeness`.
 */
class CompletenessSearch extends Completeness
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'month', 'year', 'completeness'], 'integer'],
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
        $query = Completeness::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'group_id' => $this->group_id,
            'month' => $this->month,
            'year' => $this->year,
            'completeness' => $this->completeness,
        ]);

        return $dataProvider;
    }
}
