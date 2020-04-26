<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/new", name="task_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        // is it an Ajax request ?
        if ($request->isXmlHttpRequest()) {
            $task = new Task();
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                // Par défaut, si l'utilisateur ne précise pas d'ordre, on place la nouvelle tâche créée en début de liste
                // L'utilisateur pourra modifier l'ordre par la suite côté Front
                $zOrder = !empty($task->getZOrder()) ? $task->getZOrder() : 1;
                $task->setZOrder($zOrder);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($task);
                $entityManager->flush();

                // Cleanage des z-order
                $this->cleanOrderTask();
    
                // Message Flash
                $this->addFlash(
                    'success',
                    'Tâche ajoutée.'
                );

                return $this->json([
                    'success' => true,
                    'message' => array('Ajout effectué')
                ]);
            } else {
                // Récupération des erreurs du formulaire afin de pouvoir les afficher en JS
                $errorList = array();
                $errors = $form->getErrors(true);
                foreach($errors as $error) {
                    $errorList[] = $error->getMessage();
                }
        
                return $this->json([
                    'success' => false,
                    'message' => $errorList
                ]);
            }
        }
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"POST"})
     */
    public function edit($id, Task $task, Request $request): Response
    {
        // is it an Ajax request ?
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $task->setCreatedAt(new \DateTime()); // On modifie la date de création (nécessaire pour l'ordre des tâches et non visible par l'utilisateur)
                $zOrder = !empty($task->getZOrder()) ? $task->getZOrder() : 1;
                $task->setZOrder($zOrder);
                $this->getDoctrine()->getManager()->flush();

                // Cleanage des z-order
                $this->cleanOrderTask();

                // Message Flash
                $this->addFlash(
                    'success',
                    'Tâche modifiée.'
                );
    
                return $this->json([
                    'formId' => $id,
                    'success' => true,
                    'message' => array('Modifications effectuées')
                ]);
            } else {
                // Récupération des erreurs du formulaire afin de pouvoir les afficher en JS
                $errorList = array();
                $errors = $form->getErrors(true);
                foreach($errors as $error) {
                    $errorList[] = $error->getMessage();
                }
        
                return $this->json([
                    'formId' => $id,
                    'success' => false,
                    'message' => $errorList
                ]);
            }
        }
    }

    /**
     * @Route("/{id}/delete", name="task_delete", methods={"GET"})
     */
    public function delete(Task $task): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();

        // Cleanage des z-order
        $this->cleanOrderTask();

        // Message Flash
        $this->addFlash(
            'success',
            'Tâche supprimée.'
        );

        return $this->redirectToRoute('home_page');
    }

    /**
     * Pour monter l'ordre d'une tâche d'un cran
     * 
     * @Route("/{id}/up", name="task_up", methods={"GET"})
     */
    public function toUp(Task $task, TaskRepository $taskRepo): Response
    {
        $listId = $task->getTaskList()->getId();
        $zOrder = $task->getZOrder();

        $previousTask = $taskRepo->getPreviousTask($listId, $zOrder);

        if (!is_null($previousTask)) {
            // On inverse le z_order entre la tâche déplacée
            // et celle dont elle prend la place
            $task->setZOrder($previousTask->getZOrder());
            $previousTask->setZOrder($zOrder);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('home_page');
    }

    /**
     * Pour descendre l'ordre d'une tâche d'un cran
     * 
     * @Route("/{id}/down", name="task_down", methods={"GET"})
     */
    public function toDown(Task $task, TaskRepository $taskRepo): Response
    {
        $listId = $task->getTaskList()->getId();
        $zOrder = $task->getZOrder();

        $nextTask = $taskRepo->getNextTask($listId, $zOrder);

        if (!is_null($nextTask)) {
            // On inverse le z_order entre la tâche déplacée
            // et celle dont elle prend la place
            $task->setZOrder($nextTask->getZOrder());
            $nextTask->setZOrder($zOrder);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('home_page');
    }

    /**
     * Pour déplacer une tâche dans la liste se situant à sa gauche
     * 
     * @Route("/{id}/left", name="task_left", methods={"GET"})
     */
    public function toLeft(Task $task, TaskListRepository $taskListRepo): Response
    {
        $zOrderList = $task->getTaskList()->getZOrder();

        $previousList = $taskListRepo->getPreviousList($zOrderList);

        if (!is_null($previousList)) {
            // On attribue à la tâche la liste suivante
            $task->setCreatedAt(new \DateTime()); // Modification du created_at (non visible par l'utilisateur mais nécessaire pour avoir un ordre cohérent)
            $task->setTaskList($previousList);
            $this->getDoctrine()->getManager()->flush();

            $this->cleanOrderTask();
        }

        return $this->redirectToRoute('home_page');
    }

    /**
     * Pour déplacer une tâche dans la liste se situant à sa droite
     * 
     * @Route("/{id}/right", name="task_right", methods={"GET"})
     */
    public function toRight(Task $task, TaskListRepository $taskListRepo): Response
    {
        $zOrderList = $task->getTaskList()->getZOrder();

        $nextList = $taskListRepo->getNextList($zOrderList);

        if (!is_null($nextList)) {
            // On attribue à la tâche la liste suivante
            $task->setCreatedAt(new \DateTime()); // Modification du created_at (non visible par l'utilisateur mais nécessaire pour avoir un ordre cohérent)
            $task->setTaskList($nextList);
            $this->getDoctrine()->getManager()->flush();

            $this->cleanOrderTask();
        }

        return $this->redirectToRoute('home_page');
    }

    /**
     * Permet d'ordonner et réactualiser le z_order des tâches pour éviter
     * que plusieurs tâches aient le même z_order 
     * sans pour autant ajouter de constraint d'unicité sur ce champ dans l'entity
     * (car pourrait avoir des effets de bord bloquants pour la suite des évolutions)
     * 
     */
    private function cleanOrderTask() {
        // Récupération de toutes les listes
        $lists = $this->getDoctrine()->getRepository(TaskList::class)->findAll();
        if (is_array($lists) && !empty($lists)) {
            foreach($lists as $list) {
                // Pour chaque liste, récupération de toutes les tâches
                // dans un ordre défini dans la propriété "tasks" de l'entité TaskList
                $tasks = $list->getTasks();
                foreach($tasks as $rang => $task) {
                    // On réassocie les z_order en fonction des positions
                    $task->setZOrder($rang + 1);
                    $this->getDoctrine()->getManager()->flush();
                }
            }
        }
    }
}
