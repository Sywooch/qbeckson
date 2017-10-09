<?php

namespace app\models\search;

use app\models\Invoices;
use app\models\Payers;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * InvoicesSearch represents the model behind the search form about `app\models\Invoices`.
 */
class InvoicesSearch extends Invoices
{
    public $organization;
    public $payer;
    public $mun;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number'], 'integer'],
            [['month', 'date', 'link', 'organization', 'status', 'organization_id', 'payer', 'prepayment', 'sum', 'mun'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'organization' => 'Организация',
            'mun'          => 'Муниципалитет',
            'payer'        => 'Плательщик',
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
            ->joinWith([
                'organization',
                'payer',
            ]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $dataProvider->sort->attributes['organization'] = [
            'asc'  => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['payer'] = [
            'asc'  => ['payers.name' => SORT_ASC],
            'desc' => ['payers.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'invoices.id'              => $this->id,
            'invoices.organization_id' => $this->organization_id,
            'invoices.payers_id'       => $this->payers_id,
            'invoices.number'          => $this->number,
            'invoices.date'            => $this->date,
            'invoices.prepayment'      => $this->prepayment,
            'invoices.status'          => $this->status,
        ]);

        if ($this->mun) {
            $query->andFilterWhere([
                Payers::tableName() . '.mun' => $this->mun,
            ]);
        }

        $query->andFilterWhere(['like', 'invoices.month', $this->month])
            ->andFilterWhere(['like', 'invoices.link', $this->link])
            ->andFilterWhere(['like', 'organization.name', $this->organization])
            ->andFilterWhere(['like', 'payers.name', $this->payer]);

        if (!empty($this->sum) && $this->sum !== '0,10000000') {
            $sum = explode(',', $this->sum);
            $query->andWhere([
                'AND',
                ['>=', 'invoices.sum', (float)$sum[0]],
                ['<=', 'invoices.sum', (float)$sum[1]]
            ]);
        }

        return $dataProvider;
    }
}
