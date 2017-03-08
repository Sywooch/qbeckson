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
class Payer0ContractsSearch extends Contracts
{

    
    /**
     * @inheritdoc
     */
    public $certificatenumber;
    public $organizationname;
    public $programname;
    
    public function rules()
    {
        return [
            [['id', 'number', 'status', 'status_year', 'program_id', 'organization_id', 'certificate_id'], 'integer'],
            [['date', 'status_termination', 'status_comment', 'link_doc', 'link_ofer', 'start_edu_programm', 'start_edu_contract', 'stop_edu_contract', 'certificatenumber', 'programname', 'organizationname'], 'safe'],
            [['all_funds', 'funds_cert', 'all_parents_funds', 'first_m_price', 'other_m_price', 'first_m_nprice', 'other_m_nprice', 'cert_dol', 'payer_dol', 'rezerv', 'paid', 'fontsize'], 'number'],
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
        $query->joinWith(['organization']);
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);
        
        $dataProvider->setSort([
            'attributes' => [
                'certificatenumber' => [
                    'asc' => ['certificate_id' => SORT_ASC],
                    'desc' => ['certificate_id' => SORT_DESC],
                    'label' => 'Номер сертификата',
                    'default' => SORT_ASC
                ],
                'programname' => [
                    'asc' => ['program_id' => SORT_ASC],
                    'desc' => ['program_id' => SORT_DESC],
                    'label' => 'Программа',
                    'default' => SORT_ASC
                ],
                'organizationname' => [
                    'asc' => ['organization_id' => SORT_ASC],
                    'desc' => ['organization_id' => SORT_DESC],
                    'label' => 'Организация',
                    'default' => SORT_ASC
                ],
            ]
        ]);
       
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

      //  $certificates = new Certificates();
       // $certificate = $certificates->getCertificates();

      //  $organizations = new Organization();
      //  $organization = $organizations->getOrganization();
        
        $payers = new Payers();
        $payer = $payers->getPayer();

        
        $cooperate = (new \yii\db\Query())
            ->select(['organization_id'])
            ->from('cooperate')
            ->where(['status' => 1])
            ->andwhere(['payer_id'=> $payer['id']])
            ->column();
        
        if (empty($cooperate)) {
            $cooperate = 0;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'number' => $this->number,
            'date' => $this->date,
            'certificate_id' => $this->certificate_id,
            'contracts.organization_id' => $cooperate,
            'program_id' => $this->program_id,
            'contracts.payer_id' => $payer['id'],
            'status' => '0',
            'status_termination' => $this->status_termination,
            'status_year' => $this->status_year,
            'start_edu_programm' => $this->start_edu_programm,
            'start_edu_contract' => $this->start_edu_contract,
            'stop_edu_contract' => $this->stop_edu_contract,
        ]);

        $query->andFilterWhere(['like', 'status_comment', $this->status_comment])
            ->andFilterWhere(['like', 'link_doc', $this->link_doc])
            ->andFilterWhere(['like', 'certificates.number', $this->certificatenumber])
            ->andFilterWhere(['like', 'programs.name', $this->programname])
            ->andFilterWhere(['like', 'organization.name', $this->organizationname])
            ->andFilterWhere(['like', 'link_ofer', $this->link_ofer]);

        return $dataProvider;
    }
}
