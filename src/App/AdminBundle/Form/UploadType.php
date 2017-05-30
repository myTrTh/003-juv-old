<?php

namespace App\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UploadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('folder', ChoiceType::class, array(
                        'choices'  => array(
                            'Лого турнира' => 'logo',
                            'Нашивка пользотеля в гостевой' => 'achive',
                            'Кубок в профиле пользователя' => 'cup'),
                            'expanded' => true,
                            'multiple' => false,
                            'data' => 'logo'))
                ->add('image', FileType::class, array(
                        'required' => true,
                        'constraints' => array(new File(array('mimeTypes' => array("image/png", "image/jpeg", "image/gif"), 'mimeTypesMessage' => 'Mime тип не соответсвует изображению', 'maxSize' => "100k", 'maxSizeMessage' => 'Размер файла превышен')))
                    ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AdminBundle\Entity\Upload'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_adminbundle_upload';
    }


}
