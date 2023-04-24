<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CourseRepository;
use App\Entity\Course;
use App\Form\CourseType;

class CourseController extends AbstractController
{
    // show list of courses
    #[Route('/course/all', name: 'allCourses')]
    public function allCourses(EntityManagerInterface $entityManager,
    CourseRepository $courseRepository): Response
    {
        $updates = $courseRepository->findAll();
        $courses = $courseRepository->findAll();
        return $this->render('course/index.html.twig',
        ['courses' => $courses,'updates' => $updates]);
    }
    // show list of courses
    #[Route('/home', name: 'home')]
    public function home(EntityManagerInterface $entityManager, CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findBy([], ['id' => 'DESC'], 3);
        return $this->render('course/home.html.twig', ['courses' => $courses]);
    }

    //add a new course
    #[Route('/course/add', name: 'NewCourse')]
    public function AddNewCourse(Request $request, EntityManagerInterface $entityManager, CourseRepository $courseRepository): Response
    {
        $course = new Course();
        /*$course->setTitle('PHP');
        $course->setDescription('This is a short course about PHP language 
        Modalités d’enseignement
        Modalités d’évaluation
        Cours magistraux + Ateliers pratiques (TP). 
        Volume horaire 56h
        - 4 chaque semaine
        - Questions / Réponses 
        - Support de cours disponible par email');
        $course->setDuration(56);
        $course->setLevel('Advanced');
        $course->setPrice(45.90);
        $course->setUrl('https://newuniversity.org/wp-content/uploads/2023/02/shutterstock_2241913405-1-1024x683.jpg');
        $entityManager->persist($course);
        $entityManager->flush();
        //flash message
        $this->addFlash('success', 'the course is added by success');
        return $this->redirectToRoute('allCourses');*/
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();
            $this->addFlash('success', 'the course is added by success');
            return $this->redirectToRoute('allCourses');
        }
        return $this->render('course/add.html.twig', ['form' => $form->createView(),]);
    }
    //show a course
    #[Route('/course/show/{id}', name: 'courseDetails')]
    public function show($id, CourseRepository $courseRepository): Response
    {
        $course=$courseRepository->find($id);
        return $this->render('course/show.html.twig',
        ['course' => $course]);
    }
    //show a course
    #[Route('/course/showAdmin/{id}', name: 'courseDetails')]
    public function showAdmin($id, CourseRepository $courseRepository): Response
    {
        $course=$courseRepository->find($id);
        return $this->render('course/showForAdmin.html.twig',
        ['course' => $course]);
    }
    // delete a course
    #[Route('/course/delete/{id}', name: 'course_delete')]
    public function delete($id,EntityManagerInterface $entityManager,
    CourseRepository $courseRepository): Response
    {
        $course=$courseRepository->find($id);
        if($course){
            $entityManager->remove($course);
            $entityManager->flush();
        }
        return $this->redirectToRoute('allCourse');
    }
     // find posts added by an author
     #[Route('/course/{level}', name: 'course_by_level')]
     public function searchByLevel(CourseRepository $courseRepository, $level): Response
     {
         $courses = $courseRepository->findByLevel($level);
         return $this->render('course/index.html.twig',['courses' => $courses]);
     }
     //enroll in a course
    #[Route('/course/enroll/{id}', name: 'enroll')]
    public function enroll($id, CourseRepository $courseRepository): Response
    {
        $course=$courseRepository->find($id);
        return $this->render('course/enroll.html.twig',
        ['course' => $course]);
    }
}
