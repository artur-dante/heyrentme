<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EinstellungenType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('password','password');
        $builder->add('newPassword','password');
        $builder->add('repeatedPassword','password');
        
        $builder->add('name');
        $builder->add('surname');
        
        $builder->add('phone');
        $builder->add('phonePrefix');
        
        $builder->add('iban');
        $builder->add('bic');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        //$this->setUsername($this->getName() . $this->GetSurname());
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }
    
    public function getName()
    {
        return 'form_einstellungen';
    }
}
