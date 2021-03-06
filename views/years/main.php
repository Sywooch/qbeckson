<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use app\models\Certificates;
use app\models\Contracts;
use app\models\Organization;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProgrammeModuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="years-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $index, $widget, $grid) {
            if ($model->open == 0) {
                return ['class' => 'warning'];
            }
        },
        'pjax' => true,
        'summary' => false,
        'columns' => [
            'fullname',
            ['attribute' => 'open',
                'label' => 'Зачисление',
                'format' => 'raw',
                'value' => function ($data) {
                    $price = (new \yii\db\Query())
                        ->select(['price'])
                        ->from('years')
                        ->where(['id' => $data->id])
                        ->one();

                    $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
                    $organizations = new Organization();
                    $organization = $organizations->getOrganization();
                    if ($roles['organizations'] and $organization['actual'] != 0) {
                        if ($price['price'] > 0) {
                            if ($organization->type != 4) {
                                if (!empty($organization['license_issued_dat']) and !empty($organization['fio']) and !empty($organization['position']) and !empty($organization['doc_type'])) {
                                    if ($organization['doc_type'] == 1) {
                                        if (!empty($organization['date_proxy']) and !empty($organization['number_proxy'])) {
                                            if ($data->open == 0) {
                                                return Html::a('Открыть', Url::to(['/years/start', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                            } else {
                                                return Html::a('Закрыть', Url::to(['/years/stop', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                                            }
                                        } else {
                                            return 'Заполните информацию "Для договора"';
                                        }
                                    } else {
                                        if ($data->open == 0) {
                                            return Html::a('Открыть', Url::to(['/years/start', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                        } else {
                                            return Html::a('Закрыть', Url::to(['/years/stop', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                                        }
                                    }
                                } else {
                                    return 'Заполните информацию "Для договора"';
                                }
                            } else {
                                if ($organization['doc_type'] == 1) {
                                    if ($data->open == 0) {
                                        return Html::a('Открыть', Url::to(['/years/start', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                    } else {
                                        return Html::a('Закрыть', Url::to(['/years/stop', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                                    }
                                } else {
                                    if ($data->open == 0) {
                                        return Html::a('Открыть', Url::to(['/years/start', 'id' => $data->id]), ['class' => 'btn btn-success']);
                                    } else {
                                        return Html::a('Закрыть', Url::to(['/years/stop', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                                    }
                                }
                            }
                        } else {
                            return 'Нет цены';
                        }
                    } else {
                        return 'Деятельность приостановлена';
                    }
                }
            ],
            ['attribute' => 'price',
                'format' => 'raw',
                'value' => function ($data) {
                    $price = (new \yii\db\Query())
                        ->select(['price'])
                        ->from('years')
                        ->where(['id' => $data->id])
                        ->one();

                    if ($price['price'] > 0) {
                        if ($data->open == 1) {
                            return $price['price'];
                        } else {
                            return Html::a($price['price'], Url::to(['/years/update', 'id' => $data->id]), ['class' => 'btn btn-primary']);
                        }
                    } else {
                        return Html::a('Установить цену', Url::to(['/years/update', 'id' => $data->id]), ['class' => 'btn btn-success']);
                    }
                }
            ],
            'normative_price',
            [
                'label' => 'Число обучающихся',
                'value' => function ($data) {
                    $previus = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('contracts')
                        ->where(['year_id' => $data->id])
                        ->andWhere(['status' => 1])
                        ->count();

                    return $previus;
                }
            ],
            [
                'label' => 'Предварительные записи',
                'format' => 'raw',
                'value' => function ($data) {
                    if ($data->previus == 1) {
                        return Html::a('<span class="glyphicon glyphicon-ok green"></span>', Url::to(['/years/prevstop', 'id' => $data->id]));
                    }
                    if ($data->previus == 0) {
                        return Html::a('<span class="glyphicon glyphicon-remove red"></span>', Url::to(['/years/prevstart', 'id' => $data->id]));
                    }
                }
            ],
        ],
    ]); ?>
</div>
