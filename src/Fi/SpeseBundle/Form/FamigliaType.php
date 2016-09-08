<?php

namespace Fi\SpeseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FamigliaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descrizione')
            ->add('dal', 'date', array('input' => 'datetime',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'attr' => array('class' => 'ficorebundle_datepicker'),
                    'required' => true, ))
            ->add('al', 'date', array('input' => 'datetime',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'attr' => array('class' => 'ficorebundle_datepicker'),
                    'required' => false, ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fi\SpeseBundle\Entity\famiglia',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fi_spesebundle_famiglia';
    }
}
