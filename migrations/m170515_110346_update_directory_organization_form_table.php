<?php

use yii\db\Migration;

class m170515_110346_update_directory_organization_form_table extends Migration
{
    public function up()
    {
        $arrayForms = [
            ['name' => 'Учреждение'],
            ['name' => 'Индивидуальный предприниматель'],
            ['name' => 'Автономная некоммерческая организация'],
            ['name' => 'Общество с ограниченной ответственностью'],
            ['name' => '________________________________________', 'is_separator' => 1],
            ['name' => 'Фонд'],
            ['name' => 'Полное товарищество'],
            ['name' => 'Товарищество на вере'],
            ['name' => 'Публичное акционерное общество'],
            ['name' => 'Непубличное акционерное общество'],
            ['name' => 'Унитарное предприятие, основанное на праве хозяйственного ведения'],
            ['name' => 'Унитарное предприятие, основанное на праве оперативного управления'],
            ['name' => 'Производственный кооператив'],
            ['name' => 'Потребительский кооператив'],
            ['name' => 'Государственная корпорация'],
            ['name' => 'Некоммерческое партнерство'],
            ['name' => 'Прочая форма'],
        ];
        foreach ($arrayForms as $form) {
            $this->insert("{{%directory_organization_form}}", [
                'name' => $form['name'],
                'is_separator' => isset($form['is_separator']) ? 1 : 0,
                'is_active' => 1,
            ]);
        }
    }

    public function down()
    {
        $this->truncateTable('directory_organization_form');
    }
}
