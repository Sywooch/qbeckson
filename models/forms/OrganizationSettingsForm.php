<?php

namespace app\models\forms;

use app\models\Organization;
use app\models\OrganizationContractSettings;
use Yii;
use yii\base\Model;

/**
 * Class OrganizationSettingsForm
 * @package app\models\forms
 */
class OrganizationSettingsForm extends Model
{
    public $organization_first_ending;
    public $organization_second_ending;
    public $director_name_ending;

    private $organization;
    private $model;
    private $header;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                ['organization_first_ending', 'organization_second_ending', 'director_name_ending'],
                'string', 'max' => 10
            ],
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (null !== ($model = $this->getModel()) && $this->validate()) {
            foreach ($this->getAttributes() as $key => $attribute) {
                $model->$key = $this->$key;
            }

            $header_text = $this->generateHeader();
            foreach ($this->getAttributes() as $key => $attribute) {
                $header_text = str_replace("{{{$key}}}", $attribute, $header_text);
            }
            $model->header_text = $header_text;

            return $model->save(false);
        }

        return false;
    }

    /**
     * @return string
     */
    public function generateHeader(): string
    {
        if (null === $this->header) {
            $organization = $this->getOrganization();
            if (3 > $organization->type) {
                if ($organization->doc_type == 1) {
                    $doc_type = 'доверенности от ' .
                        date('d.m.Y', strtotime($organization->date_proxy)) . ' № ' .
                        $organization->number_proxy;
                }
                if ($organization->doc_type == 2) {
                    $doc_type = 'Устава';
                }
                $text = $organization->full_name . ', осуществляющ';
                $text .= '{{organization_first_ending}} ';
                $text .= 'образовательную  деятельность на основании лицензии от ';
                $text .= date('d.m.Y', strtotime($organization->license_date)) . ' г. № ';
                $text .= $organization->license_number . ', выданной ' . $organization->license_issued_dat . ', именуем';
                $text .= '{{organization_second_ending}} ';
                $text .= 'в дальнейшем "Исполнитель", в лице '. $organization->position;
                $text .= ' ' . $organization->fio  . ', действующ';
                $text .= '{{director_name_ending}} ';
                $text .= 'на основании ';
                $text .= $doc_type;
                $text .= ', предлагает физическому лицу, являющемуся родителем (законным представителем) несовершеннолетнего, включенного в систему персонифицированного финансирования дополнительного образования на основании сертификата №0000000000, именуемого в дальнейшем "Обучающийся", именуемому в дальнейшем "Заказчик" заключить Договор-оферту';
            } else {
                if (3 === $organization->type) {
                    $text = $organization->full_name . ', осуществляющ';
                    $text .= '{{organization_first_ending}} ';
                    $text .= 'образовательную  деятельность на основании лицензии от ';
                    $text .= date('d.m.Y', strtotime($organization->license_date)) . ' г. № ';
                    $text .= $organization->license_number . ', выданной ' . $organization->license_issued_dat . ', именуем';
                }
                if (4 === $organization->type) {
                    $text = $organization->full_name . ', именуем';
                }
                $text .= '{{organization_second_ending}} ';
                $text .= 'в дальнейшем "Исполнитель", предлагает физическому лицу, являющемуся родителем (законным представителем) несовершеннолетнего, включенного в систему персонифицированного финансирования дополнительного образования на основании сертификата №0000000000, именуемого в дальнейшем "Обучающийся", именуемому в дальнейшем "Заказчик" заключить Договор-оферту';
            }
            $this->header = $text;
        }

        return $this->header;
    }

    /**
     * @param OrganizationContractSettings $model
     */
    public function setModel(OrganizationContractSettings $model)
    {
        $this->model = $model;
        foreach ($this->getAttributes() as $attribute => $value) {
            $this->$attribute = $this->model->$attribute;
        }
    }

    /**
     * @return OrganizationContractSettings
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        if (null === $this->organization) {
            $this->organization = Yii::$app->user->identity->organization;
        }

        return $this->organization;
    }
}
