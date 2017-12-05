<?php


function flashModelErr(\yii\base\Model $model, $field, $type = 'error')
{
    if ($model->hasErrors($field)) {
        flash($type, $model->getFirstError($field));
    }

    return true;
}

function flash(string $message, $type = 'error')
{
    \Yii::$app->session->setFlash($type, $message);

    return true;
}
