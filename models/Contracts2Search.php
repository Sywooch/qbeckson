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
class Contracts2Search extends Contracts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number', 'status', 'status_year'], 'integer'],
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
        $query = Contracts::find()
            ->joinWith(['payers'])
            ->where('`payers`.operator_id = ' . Yii::$app->operator->identity->id);

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

        $certificates = new Certificates();
        $certificate = $certificates->getCertificates();

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'number' => $this->number,
            'date' => $this->date,
            'certificate_id' => $this->certificate_id,
            'organization_id' => $this->organization_id,
            '`contracts`.status' => 2,
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
