<?php

namespace App\Form;

use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThemePostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextareaType::class, [
                'attr' => [
                    'class' => 'block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm',
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-semibold leading-6 text-gray-900',
                ],
                'label' => 'Titre',
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm',
                ],
                'label_attr' => [
                    'class' => 'block text-sm font-semibold leading-6 text-gray-900',
                ],
                'label' => 'Contenu texte',
            ])
            ->add('isActive', ChoiceType::class, [
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'label' => 'Actif',
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'attr' => [
                    'class' => 'flex space-x-4'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Theme::class,
        ]);
    }
}
