<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CertGroup;

/**
 * CertGroupSearch represents the model behind the search form about `app\models\CertGroup`.
 */
class CertGroupSearch extends CertGroup
{
    public $payerId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'payer_id', 'payerId', 'nominal'], 'integer'],
            [['group'], 'safe'],
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
        $query = CertGroup::find();

        $query->andFilterWhere(['payer_id' => $this->payerId]);

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
            'payer_id' => $this->payer_id,
            'nominal' => $this->nominal,
        ]);

        $query->andFilterWhere(['like', 'group', $this->group]);

        return $dataProvider;
    }
}
