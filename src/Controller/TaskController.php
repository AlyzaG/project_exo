<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="task")
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);

            return $this->redirectToRoute('home');


        }

        $taskRepository = $this->getDoctrine()
            ->getRepository(Task::class)
            ->findAll();

        return $this->render('task/index.html.twig', [
            'task' => $taskRepository,
            'form' => $form->createView(),


        ]);
    }



    /**
     * @Route("/task/update/{id}", name="updateTask")
     */
    public function edit($id, Request $request, EntityManagerInterface $entityManager){

        $task = $this->getDoctrine()
            ->getRepository(Task::class)->find($id);
        $userRepository = $this->getDoctrine()
            ->getRepository(User::class);
        $user = $userRepository;

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()){

            $user = $userRepository->find($request->request->get('taskId'));

            $newTask = $form->getData();
            $newTask->setCategorieId($user);

            $entityManager->persist($newTask);
            $entityManager->flush();

            return $this->redirectToRoute('updateTask');
        }

        return $this->render('task/update.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
