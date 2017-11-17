<?php

namespace app\models\forms;

use app\models\MunicipalTaskMatrix;
use app\models\MunicipalTaskPayerMatrixAssignment;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class MatrixForm
 * @package app\models\forms
 */
class MatrixForm extends Model
{
    private $matrix = [];

    /**
     * MatrixForm constructor.
     * @param array $config
     */
    public function __construct($payerId, $config = [])
    {
        $this->setMatrix($payerId);
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
        if ($this->getMatrix() && $this->validate()) {
            $this->model->municipal_task_matrix_id = $this->section;
            if ($this->model->save(false, ['municipal_task_matrix_id'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getMatrix()
    {
        return $this->matrix;
    }

    public function setMatrix($payerId)
    {
        $assignments = MunicipalTaskPayerMatrixAssignment::findByPayerId($payerId);
        $models = MunicipalTaskMatrix::find()->all();
        foreach ($models as $model) {
            $item = $this->setMatrixModel($payerId, $model, $assignments, MunicipalTaskPayerMatrixAssignment::CERTIFICATE_TYPE_PF);
            $this->matrix[$item->id] = $item;
            $item = $this->setMatrixModel($payerId, $model, $assignments, MunicipalTaskPayerMatrixAssignment::CERTIFICATE_TYPE_AC);
            $this->matrix[$item->id] = $item;
        }
    }

    private function setMatrixModel($payerId, $model, $assignments, $certificateType)
    {
        $id = join('_', [$payerId, $model->id, $certificateType]);
        if (isset($assignments[$id]) && $assignments[$id] instanceof MunicipalTaskPayerMatrixAssignment) {
            return $assignments[$id];
        }

        return new MunicipalTaskPayerMatrixAssignment([
            'payer_id' => $payerId,
            'matrix_id' => $model->id,
            'certificate_type' => $certificateType,
        ]);
    }
}
