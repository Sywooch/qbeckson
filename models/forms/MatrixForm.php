<?php

namespace app\models\forms;

use app\models\MunicipalTaskMatrix;
use app\models\MunicipalTaskPayerMatrixAssignment;
use yii\base\Model;
use Yii;

/**
 * Class MatrixForm
 * @package app\models\forms
 */
class MatrixForm extends Model
{
    private $matrix;

    /**
     * MatrixForm constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setMatrix();
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['section'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'section' => 'Раздел муниципального задания',
        ];
    }


    /**
     * @return bool
     */
    public function save(): bool
    {
        if ($this->getModel() && $this->validate()) {
            $this->model->municipal_task_matrix_id = $this->section;
            if ($this->model->save(false, ['municipal_task_matrix_id'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return MunicipalTaskMatrix|null
     */
    public function getMatrix()
    {
        return $this->matrix;
    }

    public function setMatrix()
    {
        $assignments = MunicipalTaskPayerMatrixAssignment::findByPayerId(Yii::$app->user->identity->payer->id);
        $models = MunicipalTaskMatrix::findByCertificateType();
        $this->matrix[MunicipalTaskMatrix::CERTIFICATE_TYPE_PREFIX_PF] = $this->setMatrixModels($models, $assignments, MunicipalTaskPayerMatrixAssignment::CERTIFICATE_TYPE_PF);
        $models = MunicipalTaskMatrix::findByCertificateType(MunicipalTaskMatrix::CERTIFICATE_TYPE_PREFIX_AC);
        $this->matrix[MunicipalTaskMatrix::CERTIFICATE_TYPE_PREFIX_AC] = $this->setMatrixModels($models, $assignments, MunicipalTaskPayerMatrixAssignment::CERTIFICATE_TYPE_AC);
    }

    private function setMatrixModels($models, $assignments, $certificateType)
    {
        $matrix = [];
        foreach ($models as $model) {
            $matrix[] = new MunicipalTaskPayerMatrixAssignment([
                'payer_id' => Yii::$app->user->identity->payer->id,
                'matrix_id' => $model->id,
                'certificate_type' => $certificateType,
            ]);
        }

        return $matrix;
    }
}
