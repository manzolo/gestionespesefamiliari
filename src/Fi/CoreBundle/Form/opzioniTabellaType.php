<?php

namespace Fi\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class opzioniTabellaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tabelle_id')
            ->add('descrizione')
            ->add('parametro')
            ->add('valore')
            ->add('tabelle')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Fi\CoreBundle\Entity\opzioniTabella'
        ));
    }

    public function getName()
    {
        return 'fi_corebundle_opzionitabellatype';
    }
}
