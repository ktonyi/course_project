<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\QuestionTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Question Type',
                'choices' => [
                    'Single-line text' => QuestionTypeEnum::STRING->value,
                    'Multi-line text' => QuestionTypeEnum::TEXT->value,
                    'Non-negative integer' => QuestionTypeEnum::INTEGER->value,
                    'Checkbox' => QuestionTypeEnum::CHECKBOX->value,
                ],
                'attr' => ['class' => 'form-control question-type'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Question Title',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('isVisibleInResults', CheckboxType::class, [
                'label' => 'Show in results table',
                'attr' => ['class' => 'form-check-input'],
                'required' => false,
            ])
            ->add('position', TextType::class, [
                'label' => 'Position',
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ]);

        
        $builder->get('type')->addModelTransformer(new CallbackTransformer(
           
            function (?QuestionTypeEnum $enum) {
                return $enum ? $enum->value : null;
            },
           
            function (?string $value) {
                return $value ? QuestionTypeEnum::from($value) : null;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}