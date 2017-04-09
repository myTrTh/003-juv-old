<?php

namespace App\TournamentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StarttournamentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
                ->add('types', ChoiceType::class, array(
                        'choices'  => array(
                            'Прогнозирование результатов матчей' => 1,
                            'Прогнозирование результативных действий футболистов' => 2),
                            'expanded' => true,
                            'multiple' => false,
                            'label' => 'ТИП ТУРНИРА',
                            'data' => 1));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\TournamentBundle\Entity\Tournament'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_corebundle_tournament';
    }


}
