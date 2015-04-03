<?php

namespace Fi\SpeseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class movimentoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tipomovimento')
                ->add('utente')
                ->add('tipologia')
                ->add('importo')
                ->add('data', 'date', array('input' => 'datetime',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'attr' => array('class' => 'ficorebundle_datepicker'),
                    'required' => false))
                ->add('nota')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Fi\SpeseBundle\Entity\movimento'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'fi_spesebundle_movimento';
    }

}
