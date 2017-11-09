<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\MunicipalTaskContract;

/**
 * MunicipalTaskContractSearch represents the model behind the search form about `app\models\MunicipalTaskContract`.
 */
class MunicipalTaskContractSearch extends MunicipalTaskContract
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'certificate_id', 'payer_id', 'organization_id', 'program_id', 'group_id', 'status', 'created_at'], 'integer'],
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
        $query = MunicipalTaskContract::find();

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
            'certificate_id' => $this->certificate_id,
            'payer_id' => $this->payer_id,
            'program_id' => $this->program_id,
            'organization_id' => $this->organization_id,
            'group_id' => $this->group_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
