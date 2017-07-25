<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Invoices;

/**
 * InvoicesSearch represents the model behind the search form about `app\models\Invoices`.
 */
class InvoicesSearch extends Invoices
{
    public $organization;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'payers_id', 'sum', 'number', 'prepayment'], 'integer'],
            [['month', 'date', 'link', 'organization', 'status', 'organization_id'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'organization' => 'Организация'
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
        $query = Invoices::find()
            ->joinWith(['organization']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

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
            'invoices.id' => $this->id,
            'invoices.organization_id' => $this->organization_id,
            'invoices.payers_id' => $this->payers_id,
            'invoices.sum' => $this->sum,
            'invoices.number' => $this->number,
            'invoices.date' => $this->date,
            'invoices.prepayment' => $this->prepayment,
            'invoices.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'invoices.month', $this->month])
            ->andFilterWhere(['like', 'invoices.link', $this->link])
            ->andFilterWhere(['like', 'organization.name', $this->organization]);

        return $dataProvider;
    }
}
