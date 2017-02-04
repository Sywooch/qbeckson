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
class Contracts4Search extends Contracts
{
    /**
     * @inheritdoc
     */
    
    public $certificatenumber;
    public $payersname;
    public $programname;
    public $yearyear;
    
    
    public function rules()
    {
        return [
            [['id', 'number', 'status', 'status_year', 'wait_termnate', 'organization_id'], 'integer'],
            [['date', 'status_termination', 'status_comment', 'link_doc', 'link_ofer', 'start_edu_programm', 'start_edu_contract', 'stop_edu_contract', 'certificatenumber', 'payersname', 'programname', 'yearyear'], 'safe'],
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
        $query->joinWith(['certificate']);
        $query->joinWith(['program']);
        $query->joinWith(['payers']);
        $query->joinWith(['organization']);
        $query->joinWith(['year']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'certificatenumber' => [
                    'asc' => ['certificate_id' => SORT_ASC],
                    'desc' => ['certificate_id' => SORT_DESC],
                    'label' => 'Номер сертификата',
                    'default' => SORT_ASC
                ],
                'payersname' => [
                    'asc' => ['payer_id' => SORT_ASC],
                    'desc' => ['payer_id' => SORT_DESC],
                    'label' => 'Плательщик',
                    'default' => SORT_ASC
                ],
                'programname' => [
                    'asc' => ['program_id' => SORT_ASC],
                    'desc' => ['program_id' => SORT_DESC],
                    'label' => 'Программа',
                    'default' => SORT_ASC
                ],
                'yearyear' => [
                    'asc' => ['year_id' => SORT_ASC],
                    'desc' => ['year_id' => SORT_DESC],
                    'label' => 'Программа',
                    'default' => SORT_ASC
                ],
            ]
        ]);
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
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
            'certificate_id' => $certificate['id'],
            'contracts.organization_id' => $this->organization_id,
            'status' => 1,
            'status_termination' => $this->status_termination,
            'status_year' => $this->status_year,
            'start_edu_programm' => $this->start_edu_programm,
            'start_edu_contract' => $this->start_edu_contract,
            'stop_edu_contract' => $this->stop_edu_contract,
            'wait_termnate' => 1,
        ]);

        $query->andFilterWhere(['like', 'status_comment', $this->status_comment])
            ->andFilterWhere(['like', 'link_doc', $this->link_doc])
            ->andFilterWhere(['like', 'certificates.number', $this->certificatenumber])
            ->andFilterWhere(['like', 'payers.name', $this->payersname])
            ->andFilterWhere(['like', 'programs.name', $this->programname])
            ->andFilterWhere(['like', 'year.year', $this->yearyear])
            ->andFilterWhere(['like', 'link_ofer', $this->link_ofer]);

        return $dataProvider;
    }
}
