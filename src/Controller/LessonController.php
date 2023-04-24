<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LessonRepository;
use App\Entity\Lesson;
use App\Form\LessonType;

class LessonController extends AbstractController
{
    // show list of lessons
    #[Route('/course/lessons', name: 'allLessons')]
    public function allCourses(EntityManagerInterface $entityManager,
    LessonRepository $lessonRepository): Response
    {
        $lessons = $lessonRepository->findAll();
        return $this->render('lesson/index.html.twig',
        ['lessons' => $lessons]);
    }
    //add a new course
    #[Route('/course/add/{course}', name: 'NewLesson')]
    public function AddNewLesson(Request $request,$course, EntityManagerInterface $entityManager, LessonRepository $lessonRepository): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lesson->setCourse($course);
            $entityManager->persist($lesson);
            $entityManager->flush();
            $this->addFlash('success', 'The lesson is added by success');
            return $this->redirectToRoute('allLessons');
        }
        return $this->render('lesson/add.html.twig', ['form' => $form->createView(),]);
    }
    //show a lesson
    #[Route('/course/lesson/{id}', name: 'lessonDetails')]
    public function more($id, LessonRepository $lessonRepository): Response
    {
        $lesson=$lessonRepository->find($id);
        return $this->render('lesson/show.html.twig',
        ['lesson' => $lesson]);
    }
    // delete a lesson
    #[Route('/course/lesson/delete/{id}', name: 'lesson_delete')]
    public function delete($id,EntityManagerInterface $entityManager,
    LessonRepository $lessonRepository): Response
    {
        $lesson=$lessonRepository->find($id);
        if($lesson){
            $entityManager->remove($lesson);
            $entityManager->flush();
            $this->addFlash('success', 'The lesson is removed by success');
        }
        return $this->redirectToRoute('allCourse');
    }
     // find posts added by an author
     #[Route('/course/lesson/{course}', name: 'lesson_by_course')]
     public function searchByCourse(LessonRepository $lessonRepository, $course): Response
     {
        $lessons = $lessonRepository->findByCourse($course);
        return $this->render('lesson/index.html.twig',['lessons' => $lessons]);
     }
}
