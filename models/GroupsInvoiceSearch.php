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
    public $invoice = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'organization_id', 'program_id', 'year_id'], 'integer'],
            [['name', 'invoice'], 'safe'],
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

        $previousMonth = strtotime('first day of previous month');
        $currentMonth = strtotime('first day of this month');

        /**@var $organization Organization */
        $organization = Yii::$app->user->identity->organization;

        $contractsQuery = Contracts::find()
            ->select(['group_id'])
            ->from('contracts')
            ->where(['organization_id' => $organization['id']])
            ->andWhere([
                'or',
                ['status' => Contracts::STATUS_ACTIVE],
                [
                    'and',
                    ['status' => Contracts::STATUS_CLOSED],
                    ['>=', 'date_termnate', date('Y-m-d', strtotime('first day of previous month'))]
                ],
            ]);

        if ($this->invoice === true) {
            $contractsQuery->andWhere(['<', 'start_edu_contract', date('Y-m-d', $currentMonth)]);
        } else {
            $contractsQuery->andWhere(['<=', 'start_edu_contract', date('Y-m-d', strtotime('last day of this month'))]);
        }

        $contractGroupIdList = $contractsQuery->column();

        if (empty($contractGroupIdList)) {
            $contractGroupIdList = 0;
        } else {
            $contractGroupIdList = array_unique($contractGroupIdList);
        }

        $query->andFilterWhere([
            'id' => $contractGroupIdList,
            'organization_id' => $this->organization_id,
            'program_id' => $this->program_id,
            'year_id' => $this->year_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
