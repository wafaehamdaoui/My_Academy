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
use App\Form\LoginType;
use App\Form\RegisterType;

class UserController extends AbstractController
{
    //show list of users
    #[Route('/users', name: 'allUsers')]
    public function allUsers(EntityManagerInterface $entityManager,
    UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('admin/users.html.twig',
        ['users' => $users]);
    }
    //add a new user
    #[Route('/user/add', name: 'NewUser')]
    public function AddNewLesson(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
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
     // find user by role
     #[Route("/user/find/{id}", name:"user_find")]
     public function searchByRole(UserRepository $userRepository, $role): Response
     {
        $users = $userRepository->findByRole($course);
        return $this->render('user/index.html.twig',['users' => $users]);
     }

     // get user's courses
     #[Route("/courses/user/{id}", name:"user_courses")]
     public function courses(UserRepository $userRepository,$id): Response
     {
        $user = $userRepository->find($id);
        return $this->render('user/profile.html.twig',['courses' => $user->getCourses(), 'user'=>$user]);
     }

     // edit a user
     #[Route("/user/edit/{id}", name:"user_edit")]
     public function edit(Request $request, User $user, EntityManagerInterface $entityManager,): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User is Edited by success');
            return $this->redirectToRoute('allUsers');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    
    //login 
    #[Route('/login', name: 'login')]
    public function login(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(LoginType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $password = $form->get('password')->getData();
            $where = [
                'email' => $email,
                'password' => $password, 
                ];
                $order = [
                'email' => 'DESC', 
                ];
                $limit = 1; 
                $offset = 0; 
                $user = $userRepository->findBy($where, $order, $limit, $offset);
                if ($user) {
                    return $this->redirectToRoute('user_courses', ['id' => $user[0]->getId()]);
                }
        }
        return $this->render('user/login.html.twig', ['form' => $form->createView(),]);
    }
    //Register => add a new user
    #[Route('/register', name: 'register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $confirmedPass = $form->get('confirmPassword')->getData();
            $password = $form->get('password')->getData();
            if ($password==$confirmedPass) {
                $user->setRole('Student');
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('user_courses', ['id' => $user->getId()]);
            }
        }
        return $this->render('user/register.html.twig', ['form' => $form->createView(),]);
    }
}
