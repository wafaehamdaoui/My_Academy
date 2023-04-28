<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Form\CategoryType;

class CategoryController extends AbstractController
{
   // show list of categories
   #[Route('/categories', name: 'Categories')]
   public function allCourses(CategoryRepository $categoryRepository): Response
   {
       $categories = $categoryRepository->findAll();
       return $this->render('admin/categories.html.twig',
       ['categories' => $categories,]);
   }
   //add a new categories
   #[Route('/category/add', name: 'addCategory')]
   public function AddNewLesson(Request $request, EntityManagerInterface $entityManager): Response
   {
       $category = new Category();
       $form = $this->createForm(CategoryType::class, $category);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $entityManager->persist($category);
           $entityManager->flush();
           $this->addFlash('success', 'The category is added by success');
           return $this->redirectToRoute('Categories');
       }
       return $this->render('category/add.html.twig', ['form' => $form->createView(),]);
   }
   // edit a Category
   #[Route("/category/edit/{id}", name:"category_edit")]
   public function edit(Request $request, Category $category, EntityManagerInterface $entityManager,): Response
  {
      $form = $this->createForm(CategoryType::class, $category);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $entityManager->persist($category);
          $entityManager->flush();
          $this->addFlash('success', 'Category is edited by success');
          return $this->redirectToRoute('Categories');
      }

      return $this->render('category/edit.html.twig', [
          'form' => $form->createView(),
          'category' => $category,
      ]);
  }
   // delete a category
   #[Route('/category/delete/{id}', name: 'category_delete')]
   public function delete($id,EntityManagerInterface $entityManager,
   CategoryRepository $categoryRepository): Response
   {
       $category=$categoryRepository->find($id);
       if($category){
           $entityManager->remove($category);
           $entityManager->flush();
           $this->addFlash('success', 'Category is removed by success');
       }
       return $this->redirectToRoute('Categories');
   }
}
