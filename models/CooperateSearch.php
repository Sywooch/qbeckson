<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CooperateSearch represents the model behind the search form about `app\models\Cooperate`.
 */
class CooperateSearch extends Cooperate
{
    public $payerName;
    public $payerMunicipality;
    public $organizationName;
    public $contractsCount;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'payer_id', 'status', 'reade'], 'integer'],
            [[
                'number', 'date', 'date_dissolution', 'payerName', 'payerMunicipality', 'organizationName',
                'contractsCount'
            ], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'payerName' => 'Плательщик',
            'organizationName' => 'Организация',
            'payerMunicipality' => 'Муниципалитет',
            'contractsCount' => 'Число договоров'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Cooperate::find()
            ->joinWith([
                'payers',
                'organization'
            ])
            ->andWhere(['organization.mun' => Yii::$app->operator->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        
        $dataProvider->sort->attributes['payers'] = [
            'asc' => ['payers.name' => SORT_ASC],
            'desc' => ['payers.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['payerMunicipality'] = [
            'asc' => ['payers.mun' => SORT_ASC],
            'desc' => ['payers.mun' => SORT_DESC],
        ];
                 
        $dataProvider->sort->attributes['organization'] = [
            'asc' => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cooperate.id' => $this->id,
            'cooperate.organization_id' => $this->organization_id,
            'cooperate.payer_id' => $this->payer_id,
            'cooperate.date' => $this->date,
            'cooperate.date_dissolution' => $this->date_dissolution,
            'cooperate.status' => $this->status,
            'cooperate.reade' => $this->reade,
        ]);

        $query->andFilterWhere(['like', 'cooperate.number', $this->number])
            ->andFilterWhere(['like', 'payers.name', $this->payerName])
            ->andFilterWhere(['like', 'payers.mun', $this->payerMunicipality])
            ->andFilterWhere(['like', 'organization.name', $this->organizationName]);

        return $dataProvider;
    }
}
