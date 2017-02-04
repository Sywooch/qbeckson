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
class ContractsStatus1onlySearch extends Contracts
{
    public $group_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number', 'status', 'status_year', 'wait_termnate', 'group_id'], 'integer'],
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
        $query = Contracts::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['group_id' => $this->group_id])
                ->andWhere(['status' => 1])
                ->column();
        
        if (empty($rows)) { $rows = 0; }
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $rows,
            'number' => $this->number,
            'date' => $this->date,
            'status' => 1,
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
