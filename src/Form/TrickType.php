<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\TrickGroup;
use App\Entity\User;
use App\Entity\Video;
use Faker\Provider\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('images', FileType::class, [
                'label' => 'Ajouter une ou plusieurs images :',
                'multiple' => true,
                'mapped' => false,
                'required'=> false
            ])
            ->add('videos', TextareaType::class, [
                'label' => "URL nouvelle vidéo :",
                'mapped' => false,
                'required'=> false
            ])
//            ->add('video', EntityType::class, [
//                'class' => Video::class,
//                'choice_label' => 'url',
//                'label' => "URL vidéo",
//                'mapped' => false,
//                'required'=> false
//            ])
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
