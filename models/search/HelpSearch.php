<?php

namespace app\models\search;

use app\models\Help;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

/**
 * HelpSearch represents the model behind the search form about `app\models\Help`.
 */
class HelpSearch extends Help
{
    public $role;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'body', 'applied_to', 'role'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [];
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
        $query = Help::find()
            ->indexBy('id')->orderBy('order_id');

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

        if (!empty($this->role)) {
            $query->andWhere(new Expression('FIND_IN_SET(:role, applied_to)'))
                ->addParams([':role' => $this->role->name]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'body', $this->body])
            ->andFilterWhere(['like', 'applied_to', $this->applied_to]);

        return $dataProvider;
    }
}
