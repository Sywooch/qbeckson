<?php

use yii\db\Migration;

class m170602_074335_add_data extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $directionData = [
            'Техническая (робототехника)',
            'Техническая (иная)',
            'Художественная',
            'Естественно-научная',
            'Социально-педагогическая',
            'Туристко-краеведческая',
            'Физкультурно-спортивная',
        ];

        foreach ($directionData as $record) {
            $this->insert('{{%directory_program_direction}}', [
                'name' => $record,
            ]);
        }

        $activityData = [
            ['direction_id' => 1, 'name' => 'Научно-технический'],
            ['direction_id' => 1, 'name' => 'Робототехника'],
            ['direction_id' => 1, 'name' => 'Конструирование'],
            ['direction_id' => 1, 'name' => 'Проектирование'],
            ['direction_id' => 1, 'name' => 'Моделирование'],
            ['direction_id' => 1, 'name' => 'Техническое творчество'],

            ['direction_id' => 2, 'name' => 'Спортивно-технический'],
            ['direction_id' => 2, 'name' => 'Научно-технический'],
            ['direction_id' => 2, 'name' => 'Инженерный '],
            ['direction_id' => 2, 'name' => 'Техническое творчество'],
            ['direction_id' => 2, 'name' => 'Фотография'],
            ['direction_id' => 2, 'name' => 'Видеография'],
            ['direction_id' => 2, 'name' => '3D-моделирование'],
            ['direction_id' => 2, 'name' => 'Техническое обслуживание'],
            ['direction_id' => 2, 'name' => 'Техническое моделирование и конструирование'],
            ['direction_id' => 2, 'name' => 'Программирование'],
            ['direction_id' => 2, 'name' => 'Киберспорт'],
            ['direction_id' => 2, 'name' => 'Мультипликация и кинопроизводство'],
            ['direction_id' => 2, 'name' => 'Мототехника'],
            ['direction_id' => 2, 'name' => 'Автотехника'],
            ['direction_id' => 2, 'name' => 'Компьютерные технологии'],
            ['direction_id' => 2, 'name' => 'Мультимедиа и компьютерная графика'],

            ['direction_id' => 3, 'name' => 'Изобразительное искусство'],
            ['direction_id' => 3, 'name' => 'Хореографическое искусство'],
            ['direction_id' => 3, 'name' => 'Театральное искусство'],
            ['direction_id' => 3, 'name' => 'Декоративно-прикладное искусство'],
            ['direction_id' => 3, 'name' => 'Вокальное творчество'],
            ['direction_id' => 3, 'name' => 'Музыкальные инструменты'],
            ['direction_id' => 3, 'name' => 'Мода и дизайн'],
            ['direction_id' => 3, 'name' => 'Литература'],
            ['direction_id' => 3, 'name' => 'Режиссура'],
            ['direction_id' => 3, 'name' => 'Ораторское искусство'],
            ['direction_id' => 3, 'name' => 'Ритмика'],
            ['direction_id' => 3, 'name' => 'Современное искусство (граффити, стрит-арт и т.д.)'],
            ['direction_id' => 3, 'name' => 'Цирковое искусство'],
            ['direction_id' => 3, 'name' => 'Кондитерское искусство'],
            ['direction_id' => 3, 'name' => 'Визаж и эстетика'],
            ['direction_id' => 3, 'name' => 'Парикмахерское искусство'],

            ['direction_id' => 4, 'name' => 'Экологический'],
            ['direction_id' => 4, 'name' => 'Биологический'],
            ['direction_id' => 4, 'name' => 'Химический'],
            ['direction_id' => 4, 'name' => 'Математический'],
            ['direction_id' => 4, 'name' => 'Зоологический'],
            ['direction_id' => 4, 'name' => 'Физический'],
            ['direction_id' => 4, 'name' => 'Медицинский'],
            ['direction_id' => 4, 'name' => 'Астрономический'],
            ['direction_id' => 4, 'name' => 'Географический'],
            ['direction_id' => 4, 'name' => 'Исследовательский'],
            ['direction_id' => 4, 'name' => 'Аквариумистика'],
            ['direction_id' => 4, 'name' => 'Растениеводство'],
            ['direction_id' => 4, 'name' => 'Фермерство'],

            ['direction_id' => 5, 'name' => 'Военно-патриотический'],
            ['direction_id' => 5, 'name' => 'Гражданский'],
            ['direction_id' => 5, 'name' => 'Юридический'],
            ['direction_id' => 5, 'name' => 'Экономический'],
            ['direction_id' => 5, 'name' => 'Педагогический'],
            ['direction_id' => 5, 'name' => 'Психологический'],
            ['direction_id' => 5, 'name' => 'Социологический'],
            ['direction_id' => 5, 'name' => 'Волонтерский'],
            ['direction_id' => 5, 'name' => 'Политический'],
            ['direction_id' => 5, 'name' => 'Игровой (организация досуга)'],
            ['direction_id' => 5, 'name' => 'Лингвистический'],
            ['direction_id' => 5, 'name' => 'Профориентационный'],
            ['direction_id' => 5, 'name' => 'Подготовительный'],
            ['direction_id' => 5, 'name' => 'Духовно-нравственный'],
            ['direction_id' => 5, 'name' => 'Языкознание'],
            ['direction_id' => 5, 'name' => 'Этика и эстетика'],
            ['direction_id' => 5, 'name' => 'Спортивное воспитание'],
            ['direction_id' => 5, 'name' => 'Социальное воспитание'],
            ['direction_id' => 5, 'name' => 'Журналистика и СМИ'],
            ['direction_id' => 5, 'name' => 'Маркетинг'],
            ['direction_id' => 5, 'name' => 'Менеджмент'],
            ['direction_id' => 5, 'name' => 'Проектная деятельность'],

            ['direction_id' => 6, 'name' => 'Краеведческий '],
            ['direction_id' => 6, 'name' => 'Исторический'],
            ['direction_id' => 6, 'name' => 'Археологический'],
            ['direction_id' => 6, 'name' => 'Этнический'],
            ['direction_id' => 6, 'name' => 'Гражданско-патриотический'],
            ['direction_id' => 6, 'name' => 'Спортивное ориентирование на местности '],
            ['direction_id' => 6, 'name' => 'Пеший туризм'],
            ['direction_id' => 6, 'name' => 'Водный туризм'],
            ['direction_id' => 6, 'name' => 'Велосипедный туризм'],
            ['direction_id' => 6, 'name' => 'Лыжный туризм'],
            ['direction_id' => 6, 'name' => 'Спортивный туризм'],
            ['direction_id' => 6, 'name' => 'Музейное дело'],

            ['direction_id' => 7, 'name' => 'Физкультурно-оздоровительный'],
            ['direction_id' => 7, 'name' => 'Беговые дисциплины легкой атлетики'],
            ['direction_id' => 7, 'name' => 'Плавание'],
            ['direction_id' => 7, 'name' => 'Гребля'],
            ['direction_id' => 7, 'name' => 'Велоспорт'],
            ['direction_id' => 7, 'name' => 'Лыжный'],
            ['direction_id' => 7, 'name' => 'Конькобежный спорт'],
            ['direction_id' => 7, 'name' => 'Другие циклические виды спорта'],
            ['direction_id' => 7, 'name' => 'Легкоатлетические виды спорта'],
            ['direction_id' => 7, 'name' => 'Метание'],
            ['direction_id' => 7, 'name' => 'Спринтерские номера программы в различных видах спорта'],
            ['direction_id' => 7, 'name' => 'Другие скоростно-силовые виды спорта'],
            ['direction_id' => 7, 'name' => 'Спортивная гимнастика'],
            ['direction_id' => 7, 'name' => 'Художественная гимнастика'],
            ['direction_id' => 7, 'name' => 'Фигурное катание на коньках'],
            ['direction_id' => 7, 'name' => 'Прыжки в воду'],
            ['direction_id' => 7, 'name' => 'Спортивные бальные танцы'],
            ['direction_id' => 7, 'name' => 'Другие сложнокоординационные виды спорта'],
            ['direction_id' => 7, 'name' => 'Борьба'],
            ['direction_id' => 7, 'name' => 'Бокс'],
            ['direction_id' => 7, 'name' => 'Футбол'],
            ['direction_id' => 7, 'name' => 'Хоккей'],
            ['direction_id' => 7, 'name' => 'Волейбол'],
            ['direction_id' => 7, 'name' => 'Теннис'],
            ['direction_id' => 7, 'name' => 'Баскетбол'],
            ['direction_id' => 7, 'name' => 'Другие спортивные игры'],
            ['direction_id' => 7, 'name' => 'Лыжное двоеборье'],
            ['direction_id' => 7, 'name' => 'Легкоатлетическое десятиборье'],
            ['direction_id' => 7, 'name' => 'Современное пятиборье'],
            ['direction_id' => 7, 'name' => 'Другие многоборья'],
            ['direction_id' => 7, 'name' => 'Армейский рукопашный бой'],
            ['direction_id' => 7, 'name' => 'Военно-спортивное многоборье'],
            ['direction_id' => 7, 'name' => 'Пожарно-прикладной спорт'],
            ['direction_id' => 7, 'name' => 'Комплексное единоборство'],
            ['direction_id' => 7, 'name' => 'Стрельба из штатного или табельного оружия'],
            ['direction_id' => 7, 'name' => 'Другие прикладные виды спорта'],
            ['direction_id' => 7, 'name' => 'Адаптивный спорт'],
            ['direction_id' => 7, 'name' => 'Шахматы'],
            ['direction_id' => 7, 'name' => 'Шашки'],
            ['direction_id' => 7, 'name' => 'Другие интеллектуальные игры'],
            ['direction_id' => 7, 'name' => 'Киберспорт'],
            ['direction_id' => 7, 'name' => 'Конный спорт'],
            ['direction_id' => 7, 'name' => 'Фитнес'],
            ['direction_id' => 7, 'name' => 'Йога'],
            ['direction_id' => 7, 'name' => 'Акробатика'],
            ['direction_id' => 7, 'name' => 'Скалолазание'],
            ['direction_id' => 7, 'name' => 'Аэробика'],
        ];

        foreach ($activityData as $record) {
            $this->insert('{{%directory_program_activity}}', [
                'direction_id' => $record['direction_id'],
                'name' => $record['name'],
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        return true;
    }
}
