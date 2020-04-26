<?php

namespace App\Form;

use App\Entity\TaskList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TaskListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Intitulé (*)',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un intitulé.'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 30,
                        'minMessage' => 'Libellé trop court. Minimum {{ limit }} caractères',
                        'maxMessage' => 'Libellé trop court. Maximum {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('z_order', IntegerType::class, [
                'label' => 'Ordre d\'affichage (*)',
                'help' => 'Minimum : 1. Maximum : 50.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ordre d\'affichage obligatoire.'
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => 50,
                        'minMessage' => "L'ordre doit au moins être de {{ limit }}.",
                        'maxMessage' => "L'ordre ne doit pas être supérieur à {{ limit }}."
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaskList::class,
        ]);
    }
}
