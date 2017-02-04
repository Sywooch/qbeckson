<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Certificates;
use app\models\Payers;

/**
 * CertificatesSearch represents the model behind the search form about `app\models\Certificates`.
 */
class CertificatesSearch extends Certificates
{
    public $payers;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'payer_id', 'actual', 'nominal', 'balance', 'contracts', 'directivity1', 'directivity2', 'directivity3', 'directivity4', 'directivity5', 'directivity6'], 'integer'],
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
        
        $query->joinWith(['payers']);
        
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);
        
        $dataProvider->sort->attributes['payers'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
            'asc' => ['payers.name' => SORT_ASC],
            'desc' => ['payers.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
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
            ->andFilterWhere(['like', 'payers.name', $this->payers]);

        return $dataProvider;
    }
}
