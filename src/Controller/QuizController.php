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

class QuizController extends AbstractController
{
    #[Route('/quiz/all', name: 'allQuizs')]
    public function allQuizs(EntityManagerInterface $entityManager,
     QuizRepository $quizRepository): Response
   {
       $quizs = $quizRepository->findAll();
       return $this->render('quiz/index.html.twig',
       ['quizs' => $quizs]);
   }
   //add a new quiz
   #[Route('/quiz/add/{course}', name: 'NewQuiz')]
   public function AddNewQuiz(Request $request,$course, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
   {
       $quiz = new Quiz();
       $form = $this->createForm(QuizType::class, $quiz);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $question->setCourse($course);
           $entityManager->persist($quiz);
           $entityManager->flush();
           $this->addFlash('success', 'Quiz added');
           return $this->redirectToRoute('allQuizs');
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
       return $this->redirectToRoute('allQuestions');
   }
    // find posts added by an author
    #[Route('/quiz/{course}', name: 'user_by_course')]
    public function searchByRole(QuestionRepository $questionRepository, $quiz): Response
    {
       $quizs = $questionRepository->findByCourse($course);
       return $this->render('quiz/index.html.twig',['quizs' => $quizs]);
    }
}


