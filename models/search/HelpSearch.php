<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Help;
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
            ->indexBy('id');

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
            $subQuery = (new \yii\db\Query())
                ->select('help_id')
                ->from('help_user_assignment')
                ->where(['user_id' => Yii::$app->user->id])
                ->andWhere('`help_user_assignment`.help_id = `help`.id');

            $query->andWhere(['not exists', $subQuery])
                ->andWhere(new Expression('FIND_IN_SET(:role, applied_to)'))
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
