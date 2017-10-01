<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contracts;
use app\models\Certificates;

/**
 * ContractsSearch represents the model behind the search form about `app\models\Contracts`.
 */
class ContractsPayerInvoiceSearch extends Contracts
{
    public $lastMonth = false;

    public $excludeContracts = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number', 'status', 'status_year'], 'integer'],
            [['date', 'status_termination', 'status_comment', 'link_doc', 'link_ofer', 'start_edu_programm', 'start_edu_contract', 'stop_edu_contract'], 'safe'],
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
        $firstDayOfMonth = $this->lastMonth == true ? strtotime('first day of previous month') : strtotime('first day of this month');
        $lastDayOfMonth = $this->lastMonth == true ? strtotime('last day of previous month') : strtotime('last day of this month');

        $query = Contracts::find()
            ->andWhere(['or', ['and', ['status' => Contracts::STATUS_ACTIVE], ['<=', 'start_edu_contract', date('Y-m-d', $lastDayOfMonth)]], ['and', ['status' => Contracts::STATUS_CLOSED], ['>', 'date_termnate', date('Y-m-d', $firstDayOfMonth)]]]);

        if ($this->lastMonth == true && !empty(trim($this->excludeContracts))) {
            $query->andWhere('id NOT IN (' . trim($this->excludeContracts) . ')');
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => false,
                'pageSize' => 999999,
            ],
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
            'number' => $this->number,
            'date' => $this->date,
            'certificate_id' => $this->certificate_id,
            'organization_id' => $this->organization_id,
            'payer_id' => $this->payer_id,
            '`contracts`.status' => $this->status,
            'status_termination' => $this->status_termination,
            'status_year' => $this->status_year,
            'start_edu_programm' => $this->start_edu_programm,
            'start_edu_contract' => $this->start_edu_contract,
            'stop_edu_contract' => $this->stop_edu_contract,
        ]);

        $query->andFilterWhere(['like', 'status_comment', $this->status_comment])
            ->andFilterWhere(['like', 'link_doc', $this->link_doc])
            ->andFilterWhere(['like', 'link_ofer', $this->link_ofer]);

        return $dataProvider;
    }
}
