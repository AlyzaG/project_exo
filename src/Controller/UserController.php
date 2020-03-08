<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{
    /**
     * @Route("/", name="user")
     */
    public function index(EntityManagerInterface $entityManager, Request $request)
    {
        $pdo = $this->getDoctrine()->getManager();
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


         if ($form->isSubmitted() && $form->isValid()){
             $user = $form->getData();

             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($user);

             return $this->redirectToRoute('home');


         }

        $userRepository = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/index.html.twig', [
            'user' => $userRepository,
            'form' => $form->createView(),


        ]);
    }

    /**
     * @Route("/user/update/{id}", name="editUser")
     */
    public function edit($id, Request $request, EntityManagerInterface $entityManager){

        $user = $this->getDoctrine()
            ->getRepository(User::class)->find($id);
        $taskRepository = $this->getDoctrine()
            ->getRepository(User::class);
        $task = $taskRepository;

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()){

            $task = $taskRepository->find($request->request->get('userId'));

            $newTask = $form->getData();
            $newTask->setCategorieId($task);

            $entityManager->persist($newTask);
            $entityManager->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('user/user.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
