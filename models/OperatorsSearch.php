<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Operators;

/**
 * OperatorsSearch represents the model behind the search form about `app\models\Operators`.
 */
class OperatorsSearch extends Operators
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'OGRN', 'INN', 'KPP', 'OKPO'], 'integer'],
            [['name', 'address_legal', 'address_actual', 'phone', 'email', 'position', 'fio'], 'safe'],
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
        $query = Operators::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'OGRN' => $this->OGRN,
            'INN' => $this->INN,
            'KPP' => $this->KPP,
            'OKPO' => $this->OKPO,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address_legal', $this->address_legal])
            ->andFilterWhere(['like', 'address_actual', $this->address_actual])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'fio', $this->fio]);

        return $dataProvider;
    }
}
