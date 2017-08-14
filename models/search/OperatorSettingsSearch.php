<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OperatorSettings;

/**
 * OperatorSettingsSearch represents the model behind the search form about `app\models\OperatorSettings`.
 */
class OperatorSettingsSearch extends OperatorSettings
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'operator_id', 'current_program_date_from', 'current_program_date_to', 'future_program_date_from', 'future_program_date_to'], 'integer'],
            [['general_document_path', 'general_document_base_url', 'general_document_name', 'extend_document_path', 'extend_document_base_url', 'extend_document_name'], 'safe'],
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
        $query = OperatorSettings::find();

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
            'operator_id' => $this->operator_id,
            'current_program_date_from' => $this->current_program_date_from,
            'current_program_date_to' => $this->current_program_date_to,
            'future_program_date_from' => $this->future_program_date_from,
            'future_program_date_to' => $this->future_program_date_to,
        ]);

        $query->andFilterWhere(['like', 'general_document_path', $this->general_document_path])
            ->andFilterWhere(['like', 'general_document_base_url', $this->general_document_base_url])
            ->andFilterWhere(['like', 'general_document_name', $this->general_document_name])
            ->andFilterWhere(['like', 'extend_document_path', $this->extend_document_path])
            ->andFilterWhere(['like', 'extend_document_base_url', $this->extend_document_base_url])
            ->andFilterWhere(['like', 'extend_document_name', $this->extend_document_name]);

        return $dataProvider;
    }
}
