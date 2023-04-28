<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use App\Form\EnrollType;
use App\Entity\User;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    //enroll in a course
    #[Route('/course/enroll/{id}', name: 'app_enroll')]
    public function enroll($id, CourseRepository $courseRepository, UserRepository $userRepository,
     Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
        $course = $courseRepository->find($id);
        
        if ($user) {
            $form = $this->createForm(EnrollType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $email=$form->get('email')->getData();
                $user=$userRepository->findOneBy(['email'=>$email,]);
                if ($user) {
                    $course->addUser($user);
                    $entityManager->persist($course);
                    $entityManager->flush();
                    $this->addFlash('success', 'Enrolled successfully');
                    return $this->redirectToRoute('user_courses', ['id' => $user->getId()]);
                }
            }
        }
        else {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('course/enroll.html.twig', ['form' => $form->createView(),'course'=>$course]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        $this->redirectToRoute('home');
    }
}
