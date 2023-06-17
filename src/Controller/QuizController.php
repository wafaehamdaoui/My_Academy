<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\QuizRepository;
use App\Repository\QuestionRepository;
use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\LessonRepository;

class QuizController extends AbstractController
{
    // get list of quizzes
    #[Route('/quiz/all', name: 'allQuizzes')]
    public function allQuizs(EntityManagerInterface $entityManager,
     QuizRepository $quizRepository): Response
   {
       $quizzes = $quizRepository->findAll();
       return $this->render('admin/quizzes.html.twig',
       ['quizzes' => $quizzes]);
   }

   //add a new quiz from quiz list page
   #[Route('/quiz/add', name: 'addQuiz')]
   public function AddQuiz(Request $request, EntityManagerInterface $entityManager, QuizRepository $quizRepository): Response
   {
       $quiz = new Quiz();
       $form = $this->createForm(QuizType::class, $quiz);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager->persist($quiz);
           $entityManager->flush();
           $this->addFlash('success', 'Quiz added by success');
           return $this->redirectToRoute('allQuizzes');
       }
       return $this->render('quiz/add.html.twig', ['form' => $form->createView(),]);
   }

   //add a new quiz from lesson page
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
           $this->addFlash('success', 'Quiz added by success');
           return $this->redirectToRoute('allQuizzes');
       }
       return $this->render('quiz/add.html.twig', ['form' => $form->createView(),]);
   }
   
   //show a quiz
   #[Route('/quizz/{id}', name: 'showQuiz')]
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
          $this->addFlash('success', 'Quiz is edited by success');
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
    // find quiz by an lesson
    #[Route('/quiz/lesson/{lesson}', name: 'quiz_by_lesson')]
    public function searchByRole(QuestionRepository $questionRepository, $quiz): Response
    {
       $quizs = $questionRepository->findByLesson($course);
       return $this->render('quiz/index.html.twig',['quizs' => $quizs]);
    }

    //take quiz
    #[Route('/takeQuiz/{id}', name: 'take_quiz')]
    public function takeQuiz(Request $request, $id, QuizRepository $quizRepository): Response
    {   $quiz = $quizRepository->find($id);
        if ($request->isMethod('POST')) {
            // process quiz submission
            $submittedAnswers = $request->request->all();
            $score = 0;
            foreach ($quiz->getQuestions() as $question) {
                $correctAnswer = $question->getResponse();
                $submittedAnswer = $submittedAnswers[$question->getId()];
                if ($correctAnswer == $submittedAnswer) {
                    $score++;
                }
            }
        
            // redirect to the quiz result page
            return $this->render('quiz/score.html.twig',['quiz' => $quiz,
            'score' => $score,
            'answers' => $submittedAnswers
        ]);
        }

        // render the quiz page
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }
}
#[Route('/Login', name: 'app_login')]
    public function index3(Request $request, CustomerRepository $compteRepository, EntityManagerInterface $entityManager): Response
    {
        $compte = new Customer();
        $panier = new Cart();
        
        $form = $this->createFormBuilder($compte)
            ->add('username', TextType::class, ['attr' => ['placeholder' => 'Enter Username']])
            ->add('password', PasswordType::class, ['attr' => ['placeholder' => 'Enter your password']])
            ->getForm();
            
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $username = $form['username']->getData();
            $password = $form['password']->getData();
            
            // Check if the user is an admin
            if ($username === 'Admin' && $password === 'admin_password') {
                // Connect to Oracle using admin credentials
                $connectionParams = [
                    'dbname' => 'ORCL',
                    'user' => 'admin_user',
                    'password' => 'admin_password',
                    'host' => 'localhost',
                    'driver' => 'oci8',
                ];
            } else {
                // Connect to Oracle using normal user credentials
                $connectionParams = [
                    'dbname' => 'ORCL',
                    'user' => 'normal_user',
                    'password' => 'normal_user_password',
                    'host' => 'localhost',
                    'driver' => 'oci8',
                ];
            }
            
            // Create the Doctrine EntityManager with the Oracle connection
            $entityManager = EntityManager::create($connectionParams, $config);
            
            // Rest of the login logic...
        }
        
        return $this->render('create_compte/index2.html.twig', [
            'form' => $form->createView()
        ]);
    }

