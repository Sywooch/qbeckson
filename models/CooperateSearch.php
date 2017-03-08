<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Cooperate;

/**
 * CooperateSearch represents the model behind the search form about `app\models\Cooperate`.
 */
class CooperateSearch extends Cooperate
{
    public $payers;
    public $payersmun;
    public $organization;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'payer_id', 'status', 'reade'], 'integer'],
            [['number', 'date', 'date_dissolution', 'payers', 'payersmun', 'organization'], 'safe'],
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
        $query = Cooperate::find();
        
        $query->joinWith(['payers']);
        $query->joinWith(['organization']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);
        
        $dataProvider->sort->attributes['payers'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
            'asc' => ['payers.name' => SORT_ASC],
            'desc' => ['payers.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['payersmun'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
            'asc' => ['payers.mun' => SORT_ASC],
            'desc' => ['payers.mun' => SORT_DESC],
        ];
        
        
                 
        $dataProvider->sort->attributes['organization'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
            'asc' => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'payer_id' => $this->payer_id,
            'date' => $this->date,
            'date_dissolution' => $this->date_dissolution,
            'status' => $this->status,
            'reade' => $this->reade,
        ]);

        $query->andFilterWhere(['like', 'number', $this->number])
        ->andFilterWhere(['like', 'payers.name', $this->payers])
        ->andFilterWhere(['like', 'payers.mun', $this->payersmun])
        ->andFilterWhere(['like', 'organization.name', $this->organization]);

        return $dataProvider;
    }
}
