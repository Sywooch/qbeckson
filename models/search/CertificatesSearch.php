<?php

namespace app\models\search;

use Yii;
use app\models\Contracts;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Certificates;

/**
 * CertificatesSearch represents the model behind the search form about `app\models\Certificates`.
 */
class CertificatesSearch extends Certificates
{
    public $onlyPayerIds;
    public $payers;
    public $enableContractsCount = false;
    public $cert_group = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id', 'user_id', 'payer_id', 'actual', 'contracts', 'directivity1', 'directivity2',
                'directivity3', 'directivity4', 'directivity5', 'directivity6', 'contractCount'
            ], 'integer', 'message' => 'Неверное значение.'],
            [['fio_child', 'number', 'name', 'soname', 'phname'], 'string'],
            [['fio_parent', 'payers', 'nominal', 'rezerv', 'balance', 'cert_group'], 'safe'],
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
        $query = Certificates::find()
            ->joinWith(['payers'])
            ->where('`payers`.operator_id = ' . Yii::$app->operator->identity->id);

        if ($this->enableContractsCount === true) {
            $subQuery = Contracts::find()
                ->select('certificate_id, COUNT(*) as contractCount')
                ->where(['status' => 1])
                ->groupBy('certificate_id');

            $query->select(['certificates.*', 'tableContractsCount.contractCount'])
                ->leftJoin(['tableContractsCount' => $subQuery], 'tableContractsCount.certificate_id = `certificates`.id');
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if (!empty($this->payers)) {
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

        //print_r($this);exit;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'actual' => $this->actual,
            'contracts' => $this->contracts,
            'directivity1' => $this->directivity1,
            'directivity2' => $this->directivity2,
            'directivity3' => $this->directivity3,
            'directivity4' => $this->directivity4,
            'directivity5' => $this->directivity5,
            'directivity6' => $this->directivity6,
        ]);

        if (!empty($this->cert_group)) {
            $query->andFilterWhere(['cert_group' => $this->cert_group]);
        }

        if (!empty($this->onlyPayerIds)) {
            $query->andFilterWhere(['payer_id' => $this->onlyPayerIds]);
        } else {
            $query->andFilterWhere(['payer_id' => $this->payer_id]);
        }

        if (!empty($this->nominal)) {
            $nominal = explode(',', $this->nominal);
            $query->andWhere(['and', ['>=', 'nominal', (int)$nominal[0]], ['<=', 'nominal', (int)$nominal[1]]]);
        }

        if (!empty($this->rezerv)) {
            $rezerv = explode(',', $this->rezerv);
            $query->andWhere(['and', ['>=', 'rezerv', (int)$rezerv[0]], ['<=', 'rezerv', (int)$rezerv[1]]]);
        }

        if (!empty($this->balance)) {
            $balance = explode(',', $this->balance);
            $query->andWhere(['and', ['>=', 'balance', (int)$balance[0]], ['<=', 'balance', (int)$balance[1]]]);
        }

        $query->andFilterWhere(['like', 'number', $this->number])
            ->andFilterWhere(['like', 'fio_child', $this->fio_child])
            ->andFilterWhere(['like', 'fio_parent', $this->fio_parent])
            ->andFilterWhere(['like', 'certificates.name', $this->name])
            ->andFilterWhere(['like', 'certificates.soname', $this->soname])
            ->andFilterWhere(['like', 'certificates.phname', $this->phname])
            ->andFilterWhere(['like', 'payers.name', $this->payers])
            ->andFilterWhere(['tableContractsCount.contractCount' => $this->contractCount]);

        return $dataProvider;
    }
}
