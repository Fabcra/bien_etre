<?php

namespace AppBundle\Form;

use AppBundle\Entity\Service;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProviderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('website')
            ->add('eMail_contact')
            ->add('phoneNo')
            ->add('tvaNo')
            ->add('services', EntityType::class, array(
                'multiple'=>true,
                'class'=>Service::class,
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('s')
                        ->where('s.valid = true');
                },
            ))
            ->add('streetName')
            ->add('addressNo')
            ->add('postalCode')
            ->add('locality')
            ->add('city')
            ->add('eMail');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Provider',
            'validation_groups' => array('providers')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_provider';
    }


}
