<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PayersSearch represents the model behind the search form about `app\models\Payers`.
 */
class PayersSearch extends Payers
{
    public $certificates;
    public $cooperates;
    public $cooperateStatus;
    public $modelName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'user_id', 'OGRN', 'INN', 'KPP', 'OKPO', 'mun', 'directionality_1_count',
                'directionality_2_count', 'directionality_3_count', 'directionality_4_count',
                'directionality_5_count', 'directionality_6_count',
            ], 'integer'],
            [[
                'id', 'name', 'address_legal', 'address_actual', 'phone', 'email', 'position', 'fio', 'directionality',
                'certificates', 'cooperates', 'cooperateStatus'
            ], 'safe'],
        ];
    }

    /**
     * @return string
     */
    public function formName()
    {
        return $this->modelName ?: '';
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
     * @param integer $pageSize
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 50)
    {
        $query = Payers::find()
            ->select([
                'payers.*',
                'COUNT(certificates.id) as certCount',
                'COUNT(cooperate.id) as coopCount'
            ])
            ->joinWith([
                'certificates',
                'cooperates'
            ])
            ->andWhere(['operator_id' => Yii::$app->operator->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => false,
                'pageSize' => $pageSize,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
                'payers.id' => $this->id,
                'payers.user_id' => $this->user_id,
                'payers.OGRN' => $this->OGRN,
                'payers.INN' => $this->INN,
                'payers.KPP' => $this->KPP,
                'payers.OKPO' => $this->OKPO,
                'payers.mun' => $this->mun,
                'payers.directionality_1_count' => $this->directionality_1_count,
                'payers.directionality_2_count' => $this->directionality_2_count,
                'payers.directionality_3_count' => $this->directionality_3_count,
                'payers.directionality_4_count' => $this->directionality_4_count,
                'payers.directionality_5_count' => $this->directionality_5_count,
                'payers.directionality_6_count' => $this->directionality_6_count,
                'cooperate.status' => $this->cooperateStatus,
            ]);

        $query->andFilterWhere(['like', 'payers.name', $this->name])
            ->andFilterWhere(['like', 'payers.address_legal', $this->address_legal])
            ->andFilterWhere(['like', 'payers.address_actual', $this->address_actual])
            ->andFilterWhere(['like', 'payers.phone', $this->phone])
            ->andFilterWhere(['like', 'payers.email', $this->email])
            ->andFilterWhere(['like', 'payers.position', $this->position])
            ->andFilterWhere(['like', 'payers.fio', $this->fio])
            ->andFilterWhere(['like', 'payers.directionality', $this->directionality]);

        if (!empty($this->certificates) && $this->certificates !== '0,150000') {
            $certCount = explode(',', $this->certificates);
            $query->andHaving(['and', ['>=', 'certCount', (int)$certCount[0]], ['<=', 'certCount', (int)$certCount[1]]]);
        }

        if (!empty($this->cooperates) && $this->cooperates !== '0,100') {
            $coopCount = explode(',', $this->cooperates);
            if ($coopCount[0] > 0) {
                $query->andWhere(['cooperate.status' => 1]);
            }
            $query->andHaving(['and', ['>=', 'coopCount', (int)$coopCount[0]], ['<=', 'coopCount', (int)$coopCount[1]]]);
        }

        $query->groupBy(['payers.id']);

        return $dataProvider;
    }
}
