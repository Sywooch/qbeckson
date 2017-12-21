<?php

namespace app\models\search;

use app\models\CertGroup;
use app\models\Certificates;
use app\models\Contracts;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CertificatesSearch represents the model behind the search form about `app\models\Certificates`.
 */
class CertificatesSearch extends Certificates
{
    public $onlyPayerIds;
    public $payer;
    public $enableContractsCount = false;
    public $cert_group = [];
    public $payerMunicipality;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id', 'user_id', 'payer_id', 'actual', 'contracts', 'directivity1', 'directivity2',
                'directivity3', 'directivity4', 'directivity5', 'directivity6', 'contractCount', 'payerMunicipality'
            ], 'integer', 'message' => 'Неверное значение.'],
            [['selectCertGroup'], 'in', 'range' => [self::TYPE_ACCOUNTING, self::TYPE_PF]],
            [['fio_child', 'number', 'name', 'soname', 'phname'], 'string'],
            [['fio_parent', 'payer', 'nominal', 'rezerv', 'balance', 'cert_group'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'payerMunicipality' => 'Муниципалитет'
        ]);
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
     * @param array $params
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 50)
    {
        $table = self::tableName();
        $query = Certificates::find()
            ->joinWith(['payers']);

        $query->andWhere(['payers.operator_id' => Yii::$app->operator->identity->id]);

        if ($this->enableContractsCount === true) {
            $contracts_table = Contracts::tableName();
            $subQuery = Contracts::find()
                ->select($contracts_table . '.[[certificate_id]], COUNT(*) as contractCount')
                ->where([$contracts_table . '.[[status]]' => 1])
                ->groupBy($contracts_table . '.[[certificate_id]]');

            $query->select(['certificates.*', 'tableContractsCount.contractCount'])
                ->leftJoin(
                    ['tableContractsCount' => $subQuery],
                    'tableContractsCount.certificate_id = `certificates`.id'
                );
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => false,
                'pageSize' => $pageSize,
            ],
        ]);

        if (!empty($this->payer)) {
            $dataProvider->sort->attributes['payer'] = [
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

        $query->andFilterWhere([
            'certificates.id' => $this->id,
            'certificates.user_id' => $this->user_id,
            'certificates.actual' => $this->actual,
            'certificates.contracts' => $this->contracts,
            'certificates.directivity1' => $this->directivity1,
            'certificates.directivity2' => $this->directivity2,
            'certificates.directivity3' => $this->directivity3,
            'certificates.directivity4' => $this->directivity4,
            'certificates.directivity5' => $this->directivity5,
            'certificates.directivity6' => $this->directivity6,
            'payers.mun' => $this->payerMunicipality,
        ]);

        $query->andFilterWhere(['like', $table . '.[[number]]', $this->number])
            ->andFilterWhere(['like', $table . '.[[fio_child]]', $this->fio_child])
            ->andFilterWhere(['like', $table . '[[fio_parent]]', $this->fio_parent])
            ->andFilterWhere(['like', 'certificates.name', $this->name])
            ->andFilterWhere(['like', 'certificates.soname', $this->soname])
            ->andFilterWhere(['like', 'certificates.phname', $this->phname])
            ->andFilterWhere(['like', 'payers.name', $this->payer])
            ->andFilterWhere(['tableContractsCount.contractCount' => $this->contractCount]);

        if (!empty($this->cert_group)) {
            $query->andFilterWhere([$table . '.[[cert_group]]' => $this->cert_group]);
        }

        if (!empty($this->onlyPayerIds)) {
            $query->andFilterWhere([$table . '.[[payer_id]]' => $this->onlyPayerIds]);
        } else {
            $query->andFilterWhere([$table . '.[[payer_id]]' => $this->payer_id]);
        }

        if (!empty($this->nominal) && $this->nominal !== '0,150000') {
            $nominal = explode(',', $this->nominal);
            $query->andWhere(['and', ['>=', $table . '.[[nominal]]', (int)$nominal[0]],
                ['<=', $table . '.[[nominal]]', (int)$nominal[1]]]);
        }

        if (!empty($this->rezerv) && $this->rezerv !== '0,150000') {
            $rezerv = explode(',', $this->rezerv);
            $query->andWhere(['and', ['>=', $table . '.[[rezerv]]', (int)$rezerv[0]],
                ['<=', $table . '.[[rezerv]]', (int)$rezerv[1]]]);
        }

        if (!empty($this->balance) && $this->balance !== '0,150000') {
            $balance = explode(',', $this->balance);
            $query->andWhere(['and', ['>=', $table . '.[[balance]]', (int)$balance[0]],
                ['<=', $table . '.[[balance]]', (int)$balance[1]]]);
        }

        if ($this->selectCertGroup) {
            $query->joinWith('certGroup');
            if ($this->selectCertGroup === self::TYPE_ACCOUNTING) {
                $query->andWhere(['>', CertGroup::tableName() . '.[[is_special]]', 0]);
            } elseif ($this->selectCertGroup === self::TYPE_PF) {
                $query->andWhere([CertGroup::tableName() . '.[[is_special]]' => null]);
            }
        }

        return $dataProvider;
    }
}
