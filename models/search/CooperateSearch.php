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
    public $payerName;
    public $payerMunicipality;
    public $organizationName;
    public $contractsCount;
    public $modelName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'payer_id', 'status', 'reade'], 'integer'],
            [['payerName', 'organizationName'], 'string'],
            [['number', 'date', 'date_dissolution', 'reject_reason', 'appeal_reason', 'created_date', 'payerMunicipality', 'contractsCount'], 'safe'],
        ];
    }

    /**
     * @return string
     */
    public function formName()
    {
        return $this->modelName;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
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
     * Creates data provider instance with search query applied
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Cooperate::find()
            ->joinWith([
                'payer',
                'organization'
            ])
            ->andWhere(['organization.mun' => Yii::$app->operator->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $dataProvider->sort->attributes['payerName'] = [
            'asc' => ['payers.name' => SORT_ASC],
            'desc' => ['payers.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['payerMunicipality'] = [
            'asc' => ['payers.mun' => SORT_ASC],
            'desc' => ['payers.mun' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['organizationName'] = [
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
            ->andFilterWhere(['like', 'organization.name', $this->organizationName])
            ->andFilterWhere(['like', 'reject_reason', $this->reject_reason])
            ->andFilterWhere(['like', 'appeal_reason', $this->appeal_reason]);

        return $dataProvider;
    }
}
