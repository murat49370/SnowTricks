<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\TrickGroup;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('create_at')
            //->add('update_at')
            ->add('title')
            ->add('content')
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Valide' => 'Valide',
                    'En attente' => 'waiting'
                ]])
            ->add('main_image')
            ->add('slug')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'pseudo'
            ])
            ->add('trick_group', EntityType::class, [
                'class' => TrickGroup::class,
                'choice_label' => 'title',
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'translation_domain' => 'forms'
        ]);
    }
}
