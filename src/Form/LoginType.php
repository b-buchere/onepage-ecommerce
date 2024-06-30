<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'label'=>'username',
                    'row_attr'=>array('class'=>"form-group p-3 pb-4"),
                ]
            )->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Mot de passe',
                    'row_attr'=>array('class'=>"form-group p-3 pb-4 pt-0"),
                ]
                )->add(
                    'submit',
                    SubmitType::class,
                );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => 'csrf_token',
            'csrf_token_id'   => 'login',
            'origin'=> null,
            'task'=>null
        ]);
    }
}
