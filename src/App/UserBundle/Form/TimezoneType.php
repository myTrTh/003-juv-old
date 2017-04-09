<?php

namespace App\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TimezoneType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        # 100 as NULL (0 and NULL different values)
        $builder->add('timezone', ChoiceType::class, array(
                'choices' => array(
                    'СМЕЩЕНИЕ' => array(
                        'Автоматически' => 100,
                        'UTC -12 Эневеток, Кваджалейн' => 12,
                        'UTC -11 Остров Мидуэй, Самоа' => 11,
                        'UTC -10 Алеутские острова' => 10,
                        'UTC -09 Аляска' => 9,
                        'UTC -08 Тихоокеанское время (США и Канада)' => 8,
                        'UTC -07 Горное время (США и Канада)' => 7,
                        'UTC -06 Центральная Америка, Мехико' => 6,
                        'UTC -05 Восточное время (США и Канада)' => 5,
                        'UTC -04 Сантьяго, Каракас' => 4,
                        'UTC -03 Буэнос-Айрес, Монтевидео' => 3,
                        'UTC -02 Среднеатлантическое время' => 2,
                        'UTC -01 Азорские острова, Кабо-Верде' => 1,
                        'UTC  00 Лондон, Лиссабон, Дублин' => 0,
                        'UTC +01 Турин, Берлин, Париж, Мадрид' => -1,
                        'UTC +02 Калининград, Киев, Рига' => -2,
                        'UTC +03 Москва, Санкт-Петербург, Волгоград' => -3,
                        'UTC +04 Астрахань, Баку, Ереван' => -4,
                        'UTC +05 Екатеринбург, Ашхабад, Ташкент' => -5,
                        'UTC +06 Омск, Астана' => -6,
                        'UTC +07 Красноярск, Томск, Банког' => -7,
                        'UTC +08 Иркутск, Пекин, Гонконг' => -8,
                        'UTC +09 Якутск, Чита, Сеул' => -9,
                        'UTC +10 Владивосток, Сидней, Мельбурн' => -10,
                        'UTC +11 Магадан, Сахалин' => -11,
                        'UTC +12 Анадырь, Петропаловск-Камчатский' => -12
                    )),
                'data' => $options['data'][0])
            );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data' => ''
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_userbundle_user';
    }


}
