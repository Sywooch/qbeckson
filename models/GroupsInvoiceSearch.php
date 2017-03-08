<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Groups;
use app\models\Organization;


/**
 * GroupsSearch represents the model behind the search form about `app\models\Groups`.
 */
class GroupsInvoiceSearch extends Groups
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'program_id', 'year_id'], 'integer'],
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
        $query = Groups::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

$contracts = (new \yii\db\Query())
                    ->select(['group_id'])
                    ->from('contracts')
                    ->where(['organization_id' => $organization['id']])
                    ->andWhere(['status' => 1])
                    ->column();

 if (empty($contracts)) { $contracts  = 0; 
} else {
             $contracts  = array_unique($contracts);
}

        $query->andFilterWhere([
            'id' => $contracts,
            'organization_id' => $this->organization_id,
            'program_id' => $this->program_id,
            'year_id' => $this->year_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
