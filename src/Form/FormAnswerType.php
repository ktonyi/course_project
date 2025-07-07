<?php

namespace App\Form;

use App\Entity\Form;
use App\Entity\Question;
use App\Entity\QuestionTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatableMessage;

class FormAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $questions = $options['questions'];

        foreach ($questions as $question) {
            $fieldOptions = [
                'label' => $question->getTitle(),
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
            ];

            switch ($question->getType()) {
                case QuestionTypeEnum::STRING:
                    $builder->add('answer_' . $question->getId(), TextType::class, $fieldOptions);
                    break;
                case QuestionTypeEnum::TEXT:
                    $builder->add('answer_' . $question->getId(), TextareaType::class, $fieldOptions);
                    break;
                case QuestionTypeEnum::INTEGER:
                    $fieldOptions['attr']['min'] = 0;
                    $builder->add('answer_' . $question->getId(), IntegerType::class, $fieldOptions);
                    break;
                case QuestionTypeEnum::CHECKBOX:
                    $fieldOptions['required'] = false;
                    $fieldOptions['attr'] = ['class' => 'form-check-input'];
                    $builder->add('answer_' . $question->getId(), CheckboxType::class, $fieldOptions);
                    break;
            }
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (\Symfony\Component\Form\FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $answers = [];

            foreach ($form->all() as $name => $field) {
                if (str_starts_with($name, 'answer_')) {
                    $questionId = str_replace('answer_', '', $name);
                    $answers[$questionId] = $field->getData();
                }
            }

            $data->setAnswers($answers);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Form::class,
            'questions' => [],
        ]);
        $resolver->setRequired('questions');
    }
}