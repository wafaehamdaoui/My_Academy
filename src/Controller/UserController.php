<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;

class UserController extends AbstractController
{
    //show list of users
    #[Route('/users', name: 'allUsers')]
    public function allUsers(EntityManagerInterface $entityManager,
    UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig',
        ['users' => $users]);
    }
    //add a new user
    #[Route('/course/add/{id}', name: 'NewUser')]
    public function AddNewLesson(Request $request,$id, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Account created successfully');
            return $this->redirectToRoute('allUsers');
        }
        return $this->render('user/add.html.twig', ['form' => $form->createView(),]);
    }
    //show a user
    #[Route('/user/{id}', name: 'userProfile')]
    public function profile($id, UserRepository $userRepository): Response
    {
        $user=$userRepository->find($id);
        return $this->render('lesson/show.html.twig',
        ['user' => $user]);
    }
    // delete a user
    #[Route('/user/delete/{id}', name: 'user_delete')]
    public function delete($id,EntityManagerInterface $entityManager,
    UserRepository $userRepository): Response
    {
        $user=$userRepository->find($id);
        if($user){
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User is removed by success');
        }
        return $this->redirectToRoute('allUsers');
    }
     // find posts added by an author
     #[Route('/course/lesson/{role}', name: 'user_by_course')]
     public function searchByRole(UserRepository $userRepository, $role): Response
     {
        $users = $userRepository->findByRole($course);
        return $this->render('user/index.html.twig',['users' => $users]);
     }
}
