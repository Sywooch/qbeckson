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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id', 'user_id', 'OGRN', 'INN', 'KPP', 'OKPO', 'mun', 'directionality_1_count',
                'directionality_2_count', 'directionality_3_count', 'directionality_4_count',
                'directionality_5_count', 'directionality_6_count',
            ], 'integer'],
            [[
                'name', 'address_legal', 'address_actual', 'phone', 'email', 'position', 'fio', 'directionality',
                'certificates', 'cooperates'
            ], 'safe'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Payers::find()
            ->select([
                'payers.*',
                'COUNT(certificates.id) as certCount',
                'COUNT(cooperate.id) as coopCount'
            ])
            ->joinWith(['certificates', 'cooperates'])
            ->andWhere(['operator_id' => Yii::$app->operator->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query
            ->andFilterWhere([
                'id' => $this->id,
                'user_id' => $this->user_id,
                'OGRN' => $this->OGRN,
                'INN' => $this->INN,
                'KPP' => $this->KPP,
                'OKPO' => $this->OKPO,
                'mun' => $this->mun,
                'directionality_1_count' => $this->directionality_1_count,
                'directionality_2_count' => $this->directionality_2_count,
                'directionality_3_count' => $this->directionality_3_count,
                'directionality_4_count' => $this->directionality_4_count,
                'directionality_5_count' => $this->directionality_5_count,
                'directionality_6_count' => $this->directionality_6_count,
            ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address_legal', $this->address_legal])
            ->andFilterWhere(['like', 'address_actual', $this->address_actual])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'directionality', $this->directionality]);

        if (!empty($this->certificates)) {
            $certCount = explode(',', $this->certificates);
            $query->andHaving(['and', ['>=', 'certCount', (int)$certCount[0]], ['<=', 'certCount', (int)$certCount[1]]]);
        }

        if (!empty($this->cooperates)) {
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
