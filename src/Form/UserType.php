<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            //->add('password')
            ->add('first_name')
            ->add('last_name')
            ->add('pseudo')
            //->add('registred_at')
            ->add('role', ChoiceType::class, [
                'choices'  => [
                    'user' => 'user',
                    'admin' => 'admin'
                ]])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Valide' => 'Valide',
                    'En attente' => 'waiting'
                ]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
