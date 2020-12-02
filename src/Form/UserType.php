<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
//            ->add('password', PasswordType::class)
//            ->add('confirm_password', PasswordType::class)
            ->add('first_name')
            ->add('last_name')
            ->add('pseudo')
            ->add('avatar', FileType::class, [
                'label' => 'Changer avatar :',
                'multiple' => false,
                'mapped' => false,
                'required'=> false
            ])
            ->add('save', SubmitType::class, ['label' => 'Enregistre'])
            //->add('registred_at')
//            ->add('status', ChoiceType::class, [
//                'choices'  => [
//                    'Valide' => 'Valide',
//                    'En attente' => 'waiting'
//                ],
//                'multiple' => false
//            ])
//            ->add('roles', ChoiceType::class, [
//                'choices'  => [
//                    'User' => 'ROLE_USER',
//                    'Admin' => 'ROLE_ADMIN'
//                ],
//                'expanded'  => false, // liste déroulante
//                'multiple'  => true // choix multiple
//                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
