<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Certificates;

/**
 * CertificatesSearch represents the model behind the search form about `app\models\Certificates`.
 */
class CertificatesSearch extends Certificates
{
    public $payers;

    public $enableContractsCount = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'payer_id', 'actual', 'nominal', 'balance', 'contracts', 'directivity1', 'directivity2', 'directivity3', 'directivity4', 'directivity5', 'directivity6', 'contractCount'], 'integer'],
            [['number', 'fio_child', 'fio_parent', 'payers'], 'safe'],
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
        $query = Certificates::find();

        if (isset($this->payer)) {
            $query->joinWith(['payers']);
        }

        if ($this->enableContractsCount === true) {
            $subQuery = \app\models\Contracts::find()
                ->select('certificate_id, COUNT(*) as contractCount')
                ->where(['status' => 1])
                ->groupBy('certificate_id');

            $query->select(['certificates.*', 'tableContractsCount.contractCount'])
                ->leftJoin(['tableContractsCount' => $subQuery], 'tableContractsCount.certificate_id = id');
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);

        if (isset($this->payer)) {
            $dataProvider->sort->attributes['payers'] = [
                'asc' => ['payers.name' => SORT_ASC],
                'desc' => ['payers.name' => SORT_DESC],
            ];
        }
        if ($this->enableContractsCount === true) {
            $dataProvider->sort->attributes['contractCount'] = [
                'asc' => ['tableContractsCount.contractCount' => SORT_ASC],
                'desc' => ['tableContractsCount.contractCount' => SORT_DESC],
            ];
        }

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'payer_id' => $this->payer_id,
            'actual' => $this->actual,
            'nominal' => $this->nominal,
            'balance' => $this->balance,
            'contracts' => $this->contracts,
            'directivity1' => $this->directivity1,
            'directivity2' => $this->directivity2,
            'directivity3' => $this->directivity3,
            'directivity4' => $this->directivity4,
            'directivity5' => $this->directivity5,
            'directivity6' => $this->directivity6,
        ]);

        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'fio_child', $this->fio_child])
            ->andFilterWhere(['like', 'fio_parent', $this->fio_parent])
            ->andFilterWhere(['like', 'payers.name', $this->payers])
            ->andFilterWhere(['tableContractsCount.contractCount' => $this->contractCount]);

        return $dataProvider;
    }
}
