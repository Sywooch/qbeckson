<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Mun;

/**
 * MunSearch represents the model behind the search form about `app\models\Mun`.
 */
class MunSearch extends Mun
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'nopc',
                    'pc',
                    'zp',
                    'dop',
                    'uvel',
                    'otch',
                    'otpusk',
                    'polezn',
                    'stav',
                    'deystv',
                    'lastdeystv',
                    'countdet',
                    'operator_id',
                    'type'
                ],
                'integer'
            ],
            [['name'], 'safe'],
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
        $query = Mun::find()
            ->where(['operator_id' => Yii::$app->operator->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->pagination->pageSize = 50;
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'nopc' => $this->nopc,
            'pc' => $this->pc,
            'zp' => $this->zp,
            'dop' => $this->dop,
            'uvel' => $this->uvel,
            'otch' => $this->otch,
            'otpusk' => $this->otpusk,
            'polezn' => $this->polezn,
            'stav' => $this->stav,
            'deystv' => $this->deystv,
            'lastdeystv' => $this->lastdeystv,
            'countdet' => $this->countdet,
        ]);

        if ($this->type) {
            $query->andFilterWhere(['type' => $this->type]);
        } else {
            $query->andFilterWhere(['type' => self::TYPE_MAIN]);
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
