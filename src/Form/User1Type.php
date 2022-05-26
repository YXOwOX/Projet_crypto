<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class User1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user_Pseudo', null, array(
              'label' => true // Will generate: "form.register.children.name.label"
          ))
            ->add('user_Password', null, array(
              'label' => true // Will generate: "form.register.children.name.label"
          ), PasswordType::class)
            ->add('user_Mail', null, array(
              'label' => true // Will generate: "form.register.children.name.label"
          ))
            ->add('user_Role', null, array(
              'label' => true // Will generate: "form.register.children.name.label"
          ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
