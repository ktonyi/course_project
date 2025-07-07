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
use Symfony\Component\Translation\TranslatableMessage;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => new TranslatableMessage('profile.question_type'),
                'choices' => [
                    'profile.question_type.string' => QuestionTypeEnum::STRING->value,
                    'profile.question_type.text' => QuestionTypeEnum::TEXT->value,
                    'profile.question_type.integer' => QuestionTypeEnum::INTEGER->value,
                    'profile.question_type.checkbox' => QuestionTypeEnum::CHECKBOX->value,
                ],
                'choice_label' => function ($choice, $key, $value) {
                    return new TranslatableMessage($key);
                },
                'attr' => ['class' => 'form-control question-type'],
            ])
            ->add('title', TextType::class, [
                'label' => new TranslatableMessage('profile.question_title'),
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => new TranslatableMessage('profile.description'),
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('isVisibleInResults', CheckboxType::class, [
                'label' => new TranslatableMessage('profile.show_in_results'),
                'attr' => ['class' => 'form-check-input'],
                'required' => false,
            ])
            ->add('position', TextType::class, [
                'label' => new TranslatableMessage('profile.position'),
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ]);

        // Добавляем DataTransformer для преобразования строки в QuestionTypeEnum и обратно
        $builder->get('type')->addModelTransformer(new CallbackTransformer(
            // Преобразование QuestionTypeEnum в строку для отображения в форме
            function (?QuestionTypeEnum $enum) {
                return $enum ? $enum->value : null;
            },
            // Преобразование строки из формы в QuestionTypeEnum
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