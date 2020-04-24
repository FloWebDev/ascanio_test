<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\Priority;
use App\Entity\TaskList;
use App\Repository\PriorityRepository;
use App\Repository\TaskListRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Intitulé'
            ])
            ->add('z_order', IntegerType::class, [
                'label' => 'Ordre d\'affichage'
            ])
            ->add('content', TextType::class, [
                'label' => 'Intitulé'
            ])
            ->add('task_list', EntityType::class, [
                'label' => 'Liste d\'appartenance',
                'class' => TaskList::class,
                'query_builder' => function (TaskListRepository $tlr) {
                    return $tlr->createQueryBuilder('tl')
                        ->orderBy('tl.z_order', 'ASC')
                        ->addOrderBy('tl.name', 'ASC');
                },
                'expanded' => false,
                'multiple' => false
            ])
            ->add('priority', EntityType::class, [
                'label' => 'Priorité',
                'class' => Priority::class,
                'query_builder' => function (PriorityRepository $pr) {
                    return $pr->createQueryBuilder('p')
                        ->orderBy('p.z_rank', 'DESC');
                },
                'expanded' => false,
                'multiple' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
