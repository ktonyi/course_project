<?php

namespace App\Form;

use App\Entity\Template;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatableMessage;

class TemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => new TranslatableMessage('profile.template_title'),
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => new TranslatableMessage('profile.description'),
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('topic', ChoiceType::class, [
                'label' => new TranslatableMessage('profile.topic'),
                'choices' => [
                    'profile.topic.education' => 'Education',
                    'profile.topic.quiz' => 'Quiz',
                    'profile.topic.other' => 'Other',
                ],
                'choice_label' => function ($choice, $key, $value) {
                    return new TranslatableMessage($key);
                },
                'attr' => ['class' => 'form-control'],
            ])
            ->add('tags', TextType::class, [
                'label' => new TranslatableMessage('profile.tags'),
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('isPublic', CheckboxType::class, [
                'label' => new TranslatableMessage('profile.public'),
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('allowedUsers', EntityType::class, [
                'label' => new TranslatableMessage('profile.allowed_users'),
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')->orderBy('u.email', 'ASC');
                },
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => QuestionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'attr' => ['class' => 'question-collection'],
                'by_reference' => false,
            ]);

        // Преобразуем массив тегов в строку для отображения
        $builder->get('tags')->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $tags = $event->getData();
            if (is_array($tags)) {
                $event->setData(implode(', ', $tags));
            } elseif ($tags === null) {
                $event->setData('');
            }
        });

        // Преобразуем строку тегов в массив перед сохранением
        $builder->get('tags')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $tags = $event->getData();
            if (is_string($tags)) {
                $tagsArray = array_filter(array_map('trim', explode(',', $tags)));
                $event->setData($tagsArray);
            } else {
                $event->setData([]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Template::class,
        ]);
    }
}