<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CourseRepository;
use App\Entity\Course;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Entity\Category;
use App\Form\CourseType;
use App\Form\CourseSearchType;
use App\Form\EnrollType;

class CourseController extends AbstractController
{
    // show list of courses
    #[Route('/course/all', name: 'allCourses')]
    public function allCourses(EntityManagerInterface $entityManager,
    CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findAll();
        return $this->render('admin/courses.html.twig',
        ['courses' => $courses,]);
    }
    // show list of courses
    #[Route('/', name: 'home')]
    public function home(Request $request, EntityManagerInterface $entityManager, CourseRepository $courseRepository, CategoryRepository $categoryRepository): Response
    {
        $freeCourses = $courseRepository->findBy(['price' => 0], ['id' => 'DESC'], 3);
        $courses = $courseRepository->findBy([], ['id' => 'DESC'], 3);
        $categories = $categoryRepository->findAll();
        $form = $this->createForm(CourseSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();
            
            $courses = $courseRepository->findByTitle(['title' => $query]);

            return $this->render('course/index.html.twig', [
                'courses' => $courses, 'title'=>'Search Result'
            ]);
        }
        return $this->render('course/home.html.twig', ['courses' => $courses,
         'categories'=>$categories, 'freeCourses'=>$freeCourses,
         'form' => $form->createView(),
        ]);
    }
     // show list of courses
     #[Route('/freeCourses', name: 'freeCourses')]
     public function freeCourses(EntityManagerInterface $entityManager, CourseRepository $courseRepository): Response
     {
         $freeCourses = $courseRepository->findBy(['price' => 0]);
         return $this->render('course/index.html.twig', ['courses' => $freeCourses,
          'title'=>'Free Courses',
         ]);
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

    // edit a course
    #[Route("/course/edit/{id}", name:"course_edit")]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager,): Response
   {
       $form = $this->createForm(CourseType::class, $course);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager->persist($course);
           $entityManager->flush();
           $this->addFlash('success', 'Course is edited by success');
           return $this->redirectToRoute('allCourses');
       }

       return $this->render('course/edit.html.twig', [
           'form' => $form->createView(),
           'course' => $course,
       ]);
   }
    //show a course
    #[Route('/course/showAdmin/{id}', name: 'courseDetailsAdmin')]
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
            $this->addFlash('success', 'Course is removed by success');
        }
        return $this->redirectToRoute('allCourses');
    }
     // find courses  by level
     #[Route('/course/{level}', name: 'course_by_level')]
     public function searchByLevel(CourseRepository $courseRepository, $level): Response
     {
         $courses = $courseRepository->findByLevel($level);
         return $this->render('course/index.html.twig',['courses' => $courses]);
     }
     // find courses  by level
     #[Route('/course/{title}', name: 'course_by_Title')]
     public function searchByTitle(CourseRepository $courseRepository, $title): Response
     {

         $courses = $courseRepository->findByTitle($title);
         return $this->render('course/index.html.twig',['courses' => $courses, 'title'=>'Search Result']);
     }
      // find course added by category
      #[Route('/course/category/{id}', name: 'findByCategory')]
      public function searchByCategory(CategoryRepository $categoryRepository,CourseRepository $courseRepository, $id): Response
      {
          $category = $categoryRepository->find($id);
          $courses = $category->getCourses();
          return $this->render('course/index.html.twig',['courses' => $courses,
          'title' => $category->getName().' Courses',
        ]);
      }
     //enroll in a course
    #[Route('/course/enroll/{id}', name: 'enroll')]
    public function enroll($id, CourseRepository $courseRepository, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $course = $courseRepository->find($id);
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
        return $this->render('course/enroll.html.twig', ['form' => $form->createView(),'course'=>$course]);
    }
    #[Route('/adminDashboard', name: 'adminDashboard')]
    public function adminDashboard(CourseRepository $courseRepository): Response
    {
    $courses = $courseRepository->findBy([], ['id' => 'DESC'], 3);
    return $this->render('dashboard.html.twig', [
        'courses' => $courses,
    ]);
    }
   
}