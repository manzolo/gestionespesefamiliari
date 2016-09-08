<?php

namespace Fi\SpeseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UtenteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('famiglia')
            ->add('nome')
            ->add('cognome')
            ->add('email')
            ->add('username')
            ->add('password')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fi\SpeseBundle\Entity\utente',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fi_spesebundle_utente';
    }
}
