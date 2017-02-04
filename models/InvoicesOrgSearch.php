<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Invoices;

/**
 * InvoicesSearch represents the model behind the search form about `app\models\Invoices`.
 */
class InvoicesOrgSearch extends Invoices
{
    public $organization;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'payers_id', 'sum', 'number', 'prepayment', 'status'], 'integer'],
            [['month', 'date', 'link', 'organization'], 'safe'],
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
        $query = Invoices::find();

        //$query->joinWith(['organization']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);

       /* $dataProvider->sort->attributes['organization'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
            'asc' => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];
        */ 
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'organization_id' => $organization['id'],
            'payers_id' => $this->payers_id,
            'sum' => $this->sum,
            'number' => $this->number,
            'date' => $this->date,
            'prepayment' => $this->prepayment,
            'status' => [0, 1, 2],
        ]);

        $query->andFilterWhere(['like', 'month', $this->month])
            ->andFilterWhere(['like', 'link', $this->link]);
           // ->andFilterWhere(['like', 'organization.name', $this->organization]);

        return $dataProvider;
    }
}
