<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Disputes;

/**
 * DisputesSearch represents the model behind the search form about `app\models\Disputes`.
 */
class DisputesSearch extends Disputes
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'contract_id', 'month', 'type', 'from'], 'integer'],
            [['text'], 'safe'],
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
        $query = Disputes::find();

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
            'contract_id' => $this->contract_id,
            'month' => $this->month,
            'type' => $this->type,
            'from' => $this->from,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text]);

        return $dataProvider;
    }
}
