<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\QuizRepository;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\LessonRepository;

class QuizController extends AbstractController
{
    #[Route('/quiz/all', name: 'allQuizzes')]
    public function allQuizs(EntityManagerInterface $entityManager,
     QuizRepository $quizRepository): Response
   {
       $quizzes = $quizRepository->findAll();
       return $this->render('admin/quizzes.html.twig',
       ['quizzes' => $quizzes]);
   }
   //add a new quiz
   #[Route('/quiz/add/{lessonId}', name: 'NewQuiz')]
   public function AddNewQuiz(Request $request,$lessonId, EntityManagerInterface $entityManager,
    QuizRepository $quizRepository, LessonRepository $lessonRepository): Response
   {
       $quiz = new Quiz();
       $form = $this->createForm(QuizType::class, $quiz);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $lesson = $lessonRepository->find($lessonId);
           $quiz->setLesson($lesson);
           $entityManager->persist($quiz);
           $entityManager->flush();
           $this->addFlash('success', 'Quiz added');
           return $this->redirectToRoute('allQuizzes');
       }
       return $this->render('quiz/add.html.twig', ['form' => $form->createView(),]);
   }
   //add a new quiz
   #[Route('/quiz/add', name: 'addQuiz')]
   public function AddQuiz(Request $request, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
   {
       $quiz = new Quiz();
       $form = $this->createForm(QuizType::class, $quiz);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager->persist($quiz);
           $entityManager->flush();
           $this->addFlash('success', 'Quiz added');
           return $this->redirectToRoute('allQuizzes');
       }
       return $this->render('quiz/add.html.twig', ['form' => $form->createView(),]);
   }
   //show a quiz
   #[Route('/quiz/{id}', name: 'showQuiz')]
   public function show($id, QuizRepository $quizRepository): Response
   {
       $quiz=$quizRepository->find($id);
       return $this->render('quiz/show.html.twig',
       ['quiz' => $quiz]);
   }
   // edit a quiz
   #[Route("/quiz/edit/{id}", name:"quiz_edit")]
   public function edit(Request $request, Quiz $quiz, EntityManagerInterface $entityManager,): Response
  {
      $form = $this->createForm(QuizType::class, $quiz);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $entityManager->persist($quiz);
          $entityManager->flush();

          return $this->redirectToRoute('allQuizzes');
      }

      return $this->render('quiz/edit.html.twig', [
          'form' => $form->createView(),
          'quiz' => $quiz,
      ]);
  }
   // delete a quiz
   #[Route('/quiz/delete/{id}', name: 'quiz_delete')]
   public function delete($id,EntityManagerInterface $entityManager,
   QuizRepository $quizRepository): Response
   {
       $quiz=$quizRepository->find($id);
       if($quiz){
           $entityManager->remove($quiz);
           $entityManager->flush();
           $this->addFlash('success', 'Quiz is removed by success');
       }
       return $this->redirectToRoute('allQuizzes');
   }
    // find posts added by an author
    #[Route('/quiz/{course}', name: 'user_by_course')]
    public function searchByRole(QuestionRepository $questionRepository, $quiz): Response
    {
       $quizs = $questionRepository->findByCourse($course);
       return $this->render('quiz/index.html.twig',['quizs' => $quizs]);
    }
}


