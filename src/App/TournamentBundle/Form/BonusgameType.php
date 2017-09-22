<?php

namespace App\TournamentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BonusgameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('team1', CollectionType::class, array(
                              'entry_type' => ChoiceType::class,
                              'allow_add' => true,
                              'allow_delete' => true))
                ->add('team2', CollectionType::class, array(
                              'entry_type' => ChoiceType::class,
                              'allow_add' => true,
                              'allow_delete' => true))
                ->add('save', SubmitType::class, array('label' => 'Сохранить'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults(array('data' => ''));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_corebundle_forecast';
    }


}
