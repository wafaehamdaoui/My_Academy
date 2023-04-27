<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LessonRepository;
use App\Entity\Lesson;
use App\Repository\CourseRepository;
use App\Entity\Course;
use App\Form\LessonType;

class LessonController extends AbstractController
{
    // show list of lessons
    #[Route('/allLessons', name: 'allLessons')]
    public function allCourses(EntityManagerInterface $entityManager,
    LessonRepository $lessonRepository): Response
    {
        $lessons = $lessonRepository->findAll();
        return $this->render('admin/lessons.html.twig',
        ['lessons' => $lessons]);
    }
    //add a new course
    #[Route('/lesson/add/{courseId}', name: 'NewLesson')]
    public function AddNewLesson(Request $request,$courseId, EntityManagerInterface $entityManager,
     LessonRepository $lessonRepository, CourseRepository $courseRepository): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $course = $courseRepository->find($courseId);
            $lesson->setCourse($course);
            $entityManager->persist($lesson);
            $entityManager->flush();
            $this->addFlash('success', 'The lesson is added by success');
            return $this->redirectToRoute('courseDetails',['id'=>$courseId]);
        }
        return $this->render('lesson/add.html.twig', ['form' => $form->createView(),]);
    }
    //add a new course
    #[Route('/lesson/add', name: 'addLesson')]
    public function AddLesson(Request $request, EntityManagerInterface $entityManager,
     LessonRepository $lessonRepository): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();
            $this->addFlash('success', 'Lesson is added by success');
            return $this->redirectToRoute('allLessons');
        }
        return $this->render('lesson/add.html.twig', ['form' => $form->createView(),]);
    }
    //show a lesson
    #[Route('/lesson/{id}', name: 'lessonDetails')]
    public function more($id, LessonRepository $lessonRepository): Response
    {
        $lesson=$lessonRepository->find($id);
        return $this->render('lesson/show.html.twig',
        ['lesson' => $lesson]);
    }
    // edit a lesson
    #[Route("/lesson/edit/{id}", name:"lesson_edit")]
    public function edit(Request $request, Lesson $lesson, EntityManagerInterface $entityManager,): Response
   {
       $form = $this->createForm(LessonType::class, $lesson);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager->persist($lesson);
           $entityManager->flush();
           $this->addFlash('success', 'Lesson is edited by success');
           return $this->redirectToRoute('allLessons');
       }

       return $this->render('lesson/edit.html.twig', [
           'form' => $form->createView(),
           'lesson' => $lesson,
       ]);
   }
    // delete a lesson
    #[Route('/lesson/delete/{id}', name: 'lesson_delete')]
    public function delete($id,EntityManagerInterface $entityManager,
    LessonRepository $lessonRepository): Response
    {
        $lesson=$lessonRepository->find($id);
        if($lesson){
            $entityManager->remove($lesson);
            $entityManager->flush();
            $this->addFlash('success', 'Lesson is removed by success');
        }
        return $this->redirectToRoute('allLessons');
    }
     // find posts added by an author
     #[Route('/lesson/{course}', name: 'lesson_by_course')]
     public function searchByCourse(LessonRepository $lessonRepository, $course): Response
     {
        $lessons = $lessonRepository->findByCourse($course);
        return $this->render('lesson/index.html.twig',['lessons' => $lessons]);
     }
}
