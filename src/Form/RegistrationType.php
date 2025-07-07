<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => new TranslatableMessage('register.email'),
                'attr' => ['class' => 'form-control', 'required' => 'required'],
            ])
            ->add('name', TextType::class, [
                'label' => new TranslatableMessage('register.name'),
                'attr' => ['class' => 'form-control', 'required' => 'required'],
            ])
            ->add('password', PasswordType::class, [
                'label' => new TranslatableMessage('register.password'),
                'attr' => ['class' => 'form-control', 'required' => 'required'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}