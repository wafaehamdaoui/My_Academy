<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\QuestionRepository;
use App\Entity\Question;
use App\Form\QuestionType;


class QuestionController extends AbstractController
{
   //show list of questions
   #[Route('/question/all', name: 'allQuestions')]
   public function allQuestions(QuestionRepository $questionRepository): Response
   {
       $questions = $questionRepository->findAll();
       return $this->render('admin/questions.html.twig',
       ['questions' => $questions]);
   }
   //add a new question
   #[Route('/question/add/{quizId}', name: 'NewQuestion')]
   public function AddNewQestion(Request $request,$quizId, EntityManagerInterface $entityManager,
     QuizRepository $quizRepository): Response
   {
       $question = new Question();
       $form = $this->createForm(QuestionType::class, $question);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $quiz = $quizRepository->find($quizId);
           $question->setQuiz($quiz);
           $entityManager->persist($question);
           $entityManager->flush();
           $this->addFlash('success', 'question added');
           return $this->redirectToRoute('allQuestions');
       }
       return $this->render('question/add.html.twig', ['form' => $form->createView(),]);
   }
   //add a new question
   #[Route('/quiz/question/add', name: 'addQuestion')]
   public function AddQuestion(Request $request, EntityManagerInterface $entityManager): Response
   {
       $question = new Question();
       $form = $this->createForm(QuestionType::class, $question);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager->persist($question);
           $entityManager->flush();
           $this->addFlash('success', 'Question added by success');
           return $this->redirectToRoute('allQuestions');
       }
       return $this->render('question/add.html.twig', ['form' => $form->createView(),]);
   }
   //show a question
   #[Route('/question/{id}', name: 'getQuestion')]
   public function show($id, QuestionRepository $questionRepository): Response
   {
       $question=$questionRepository->find($id);
       return $this->render('question/show.html.twig',
       ['question' => $question]);
   }
   // edit a Question
   #[Route("/question/edit/{id}", name:"question_edit")]
   public function edit(Request $request, Question $question, EntityManagerInterface $entityManager,): Response
  {
      $form = $this->createForm(QuestionType::class, $question);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $entityManager->persist($question);
          $entityManager->flush();
          $this->addFlash('success', 'Question is edited by success');
          return $this->redirectToRoute('allQuestions');
      }

      return $this->render('question/edit.html.twig', [
          'form' => $form->createView(),
          'question' => $question,
      ]);
  }
   // delete a question
   #[Route('/question/delete/{id}', name: 'question_delete')]
   public function delete($id,EntityManagerInterface $entityManager,
   QuestionRepository $questionRepository): Response
   {
       
       $question=$questionRepository->find($id);
       if($question){
           $entityManager->remove($question);
           $entityManager->flush();
           $this->addFlash('success', 'Question is removed by success');
       }
       return $this->redirectToRoute('allQuestions');
   }
    // find question by quiz
    #[Route('/question/{quiz}', name: 'question_by_quiz')]
    public function searchByRole(QuestionRepository $questionRepository, $quiz): Response
    {
       $questions = $questionRepository->findByQuiz($quiz);
       return $this->render('question/index.html.twig',['questions' => $questions]);
    }
}
