<?php

namespace App\VoteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\VotesBundle\Entity\Option;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OptionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description');
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'App\VotesBundle\Entity\Options');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\VotesBundle\Entity\Options',
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_votesbundle_options';
    }


}