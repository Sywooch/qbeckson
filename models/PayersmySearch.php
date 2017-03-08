<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Payers;
use app\models\Cooperate;

/**
 * PayersSearch represents the model behind the search form about `app\models\Payers`.
 */
class PayersmySearch extends Payers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'OGRN', 'INN', 'KPP', 'OKPO', 'directionality_1_count', 'directionality_2_count', 'directionality_3_count', 'directionality_4_count', 'directionality_5_count', 'directionality_6_count'], 'integer'],
            [['name', 'address_legal', 'address_actual', 'phone', 'email', 'position', 'fio', 'directionality', 'mun'], 'safe'],
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
        $query = Payers::find();

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

        $cooperates = new Cooperate();
        $cooperate = $cooperates->getCooperatePayers();
        if (empty($cooperate)) { $cooperate = 0; }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $cooperate,
            'user_id' => $this->user_id,
            'OGRN' => $this->OGRN,
            'INN' => $this->INN,
            'KPP' => $this->KPP,
            'OKPO' => $this->OKPO,
            'directionality_1_count' => $this->directionality_1_count,
            'directionality_2_count' => $this->directionality_2_count,
            'directionality_3_count' => $this->directionality_3_count,
            'directionality_4_count' => $this->directionality_4_count,
            'directionality_5_count' => $this->directionality_5_count,
            'directionality_6_count' => $this->directionality_6_count,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address_legal', $this->address_legal])
            ->andFilterWhere(['like', 'address_actual', $this->address_actual])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'mun', $this->mun])
            ->andFilterWhere(['like', 'directionality', $this->directionality]);

        return $dataProvider;
    }
}
