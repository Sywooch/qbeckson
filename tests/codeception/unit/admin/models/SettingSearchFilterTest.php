<?php

namespace app\tests\codeception\unit\admin\models;

use app\models\SettingsSearchFilters;
use app\models\UserIdentity;

class SettingSearchFilterTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testRoleValidate()
    {
        $model = new SettingsSearchFilters([
            'role' => UserIdentity::ROLE_OPERATOR,
        ]);
        expect('ПРоходит валидация роли из списка ролей', $model->validate(['role']))->true();
        $model->role = 'some_danger_role';
        expect('НЕ проходит валидация роли не из списка ролей', $model->validate(['role']))->false();
    }

    public function testSpliter()
    {
        $model = new SettingsSearchFilters();
        $str1 = 'first,second,';
        $arr1 = ['first', 'second', ''];
        $wrongArr1 = ['first', 'second', 'aaaaaaaa'];

        $str2 = 'first second ';
        $str3 = "first\tsecond\t";
        $str4 = "first\tsecond,";

        expect('ожидаем эквивалентный массив при разбиении запятыми', $model->split($str1))->equals($arr1);
        expect('ожидаем не эквивалентный массив при разбиении запятыми', $model->split($str1))->notEquals($wrongArr1);
        expect('ожидаем не эквивалентный массив при разбиении пробелами', $model->split($str2))->notEquals($arr1);
        expect('ожидаем не эквивалентный массив при разбиении табами', $model->split($str3))->notEquals($arr1);
        expect('ожидаем не эквивалентный массив при разбиении табами и запятой', $model->split($str4))
            ->notEquals($arr1);
    }

    // tests

    public function testColumnsUnique()
    {
        $correctColumns = 'col1,col2,col3,col4,col5';
        $unCorrectColumns = 'col1,col2,col3,col4,col1';
        $unCorrectColumns1 = 'col1,col3,col3,col4,col1';

        $model = new SettingsSearchFilters();
        $model->inaccessible_columns = $correctColumns;
        $model->table_columns = $correctColumns;

        expect(
            'модель валидируется с корректными столбцами',
            $model->validate(['inaccessible_columns', 'table_columns'])
        )->true();

        $model->inaccessible_columns = $unCorrectColumns;
        expect(
            'модель не валидируется с ошибочными столбцами',
            $model->validate(['inaccessible_columns', 'table_columns'])
        )->false();

        $model->inaccessible_columns = $correctColumns;
        $model->table_columns = $unCorrectColumns;
        expect(
            'модель не валидируется с ошибочными столбцами',
            $model->validate(['inaccessible_columns', 'table_columns'])
        )->false();

        $model->inaccessible_columns = $correctColumns;
        $model->table_columns = $unCorrectColumns1;
        expect(
            'модель не валидируется с ошибочными столбцами',
            $model->validate(['inaccessible_columns', 'table_columns'])
        )->false();

        $model->inaccessible_columns = $unCorrectColumns1;
        $model->table_columns = $unCorrectColumns1;
        expect(
            'модель не валидируется с ошибочными столбцами',
            $model->validate(['inaccessible_columns', 'table_columns'])
        )->false();

        expect(
            'присутствует сообщение об ошибке inaccessible_columns',
            $model->getFirstError('inaccessible_columns')
        )
            ->equals('Столбцы должны быть уникальны!');
        expect(
            'присутствует сообщение об ошибке table_columns',
            $model->getFirstError('table_columns')
        )
            ->equals('Столбцы должны быть уникальны!');
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}
