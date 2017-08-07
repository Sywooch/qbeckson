<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cooperate;

/**
 * CooperateSearch represents the model behind the search form about `app\models\Cooperate`.
 */
class CooperateSearch extends Cooperate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'payer_id', 'status', 'reade'], 'integer'],
            [['number', 'date', 'date_dissolution', 'reject_reason', 'appeal_reason', 'created_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Cooperate::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'payer_id' => $this->payer_id,
            'date' => $this->date,
            'date_dissolution' => $this->date_dissolution,
            'status' => $this->status,
            'reade' => $this->reade,
            'created_date' => $this->created_date,
        ]);

        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'reject_reason', $this->reject_reason])
            ->andFilterWhere(['like', 'appeal_reason', $this->appeal_reason]);

        return $dataProvider;
    }
}
