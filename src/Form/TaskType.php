<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\Priority;
use App\Entity\TaskList;
use Symfony\Component\Form\FormEvent;
use App\Repository\PriorityRepository;
use App\Repository\TaskListRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $listener = function (FormEvent $event) {
            $form = $event->getForm();
            $task = $event->getData();

            if (!is_null($task->getId())) {
                // Si ID, alors il s'agit d'une modification
                $form->remove('task_list')
                ;
            }
        };

        $builder
            ->add('task_list', EntityType::class, [
                'label' => 'Liste d\'appartenance (*)',
                'class' => TaskList::class,
                'query_builder' => function (TaskListRepository $tlr) {
                    return $tlr->createQueryBuilder('tl')
                        ->orderBy('tl.z_order', 'ASC')
                        ->addOrderBy('tl.name', 'ASC');
                },
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner une liste associée.'
                    ]),
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Intitulé (*)',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un intitulé.'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 60,
                        'minMessage' => 'Libellé trop court. Minimum {{ limit }} caractères',
                        'maxMessage' => 'Libellé trop long. Maximum {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Détails (*)',
                'attr' => [
                    'placeholder' => "",
                    'rows' => 5
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir les détails associées à la tâche.'
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 1000,
                        'minMessage' => 'Minimum {{ limit }} caractères',
                        'maxMessage' => 'Maximum {{ limit }} caractères.'
                    ])
                ]
            ])
            ->add('z_order', IntegerType::class, [
                'label' => 'Ordre d\'affichage',
                'help' => 'Si aucun ordre d\'affichage n\'est saisi (min 1, max 200), la tâche sera placée en début de liste.',
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => 200,
                        'minMessage' => "L'ordre doit au moins être de {{ limit }}.",
                        'maxMessage' => "L'ordre ne doit pas être supérieur à {{ limit }}."
                    ])
                ]
            ])
            ->add('priority', EntityType::class, [
                'label' => 'Priorité (*)',
                'class' => Priority::class,
                'query_builder' => function (PriorityRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.z_rank', 'DESC');
                },
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner une priorité.'
                    ]),
                ]
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, $listener)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
