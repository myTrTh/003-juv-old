<?php

namespace App\GuestbookBundle\Form;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AchiveType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('user', ChoiceType::class, array(
                      'choices' => $options['data'][1],
                      'multiple' => false)
                    )
                ->add('description')
                ->add('image', ChoiceType::class, array(
                      'choices' => array($options['data'][0]),
                      'expanded' => true,
                      'multiple' => false)
                    );
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
        return 'app_guestbookbundle_achive';
    }


}
