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
class ContractsDecInvoiceSearch extends Contracts
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
        $query = Contracts::find();

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

        $lmonth = Yii::$app->params['decemberNumber'];
        $start = date('Y') . '-' . $lmonth . '-01';
        $cal_days_in_month = cal_days_in_month(CAL_GREGORIAN, $lmonth, date('Y'));
        $stop = date('Y') . '-' . $lmonth . '-' . $cal_days_in_month;

        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['<=', 'start_edu_contract', $stop])
            ->andWhere(['>=', 'stop_edu_contract', $start])
            ->andWhere(['organization_id' => $organization->id])
            ->andWhere(['payer_id' => $this->payer_id])
            ->andWhere(['`contracts`.status' => 1])
            ->andWhere(['>', 'all_funds', 0])
            ->column();

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $contracts,
            'number' => $this->number,
            'date' => $this->date,
            'certificate_id' => $this->certificate_id,
            'organization_id' => $organization['id'],
            'payer_id' => $this->payer_id,
            '`contracts`.status' => Contracts::STATUS_ACTIVE,
            'status_termination' => $this->status_termination,
            'status_year' => $this->status_year,
            'start_edu_programm' => $this->start_edu_programm,
            'start_edu_contract' => $this->start_edu_contract,
            'stop_edu_contract' => $this->stop_edu_contract,
        ]);

        return $dataProvider;
    }
}
