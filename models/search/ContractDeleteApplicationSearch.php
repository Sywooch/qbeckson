<?php

namespace app\models\search;

use app\models\Certificates;
use app\models\ContractDeleteApplication;
use app\models\Contracts;
use yii\data\ActiveDataProvider;

/**
 * ContractDeleteApplicationSearch represents the model behind the search form about `app\models\ContractDeleteApplication`.
 */
class ContractDeleteApplicationSearch extends ContractDeleteApplication
{
    public $certificateNumber;
    public $contractNumber;
    public $contractDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['certificateNumber', 'status'], 'integer'],
            [['status'], 'in', 'range' => [self::STATUS_WAITING, self::STATUS_CONFIRMED, self::STATUS_REFUSED]],
            [['contractNumber'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),
            [
                'contractNumber' => 'Номер договора',
                'certificateNumber' => 'Номер сертификата',
                'contractDate' => 'Дата договора',
            ]);
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 50)
    {
        $query = self::find()->joinWith('contract.certificate');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => false,
                'pageSize' => $pageSize,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            self::tableName() . '.[[status]]' => $this->status,
            Certificates::tableName() . '.[[number]]' => $this->certificateNumber,
        ]);

        $query->andFilterWhere(['like', Contracts::tableName() . '.[[number]]', $this->contractNumber]);

        return $dataProvider;
    }
}
