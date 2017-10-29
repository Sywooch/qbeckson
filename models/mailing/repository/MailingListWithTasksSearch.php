<?php

namespace app\models\mailing\repository;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MailingListWithTasksSearch represents the model behind the search form about
 * `app\models\mailing\repository\MailingListWithTasks`.
 */
class MailingListWithTasksSearch extends MailingListWithTasks
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', /*'created_by',*/
                'created_at'], 'integer'],
            [['subject', 'message'], 'safe'],
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
        $query = MailingListWithTasks::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
