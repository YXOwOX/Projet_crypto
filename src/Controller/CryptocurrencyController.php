<?php

namespace App\Controller;

use App\Entity\Cryptocurrency;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CryptocurrencyRepository;

/**
 * @Route("/cryptocurrency")
 */
class CryptocurrencyController extends AbstractController
{

    /**
     * @Route("/", name="app_cryptocurrency")
     */
     public function listAction(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
     {
       //$query = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findAll();

         $dql   = "SELECT a FROM App\Entity\Cryptocurrency a";
         $query = $em->createQuery($dql);

         $pagination = $paginator->paginate(
             $query, /* query NOT result */
             $request->query->getInt('page', 1), /*page number*/
             50 /*limit per page*/
         );

         // parameters to template
         return $this->render('cryptocurrency/list.html.twig', [
             'pagination' => $pagination,
         ]);
    }

    /**
     * @Route("/category/{cat}", name = "app_category")
     */
     public function listCategory(PaginatorInterface $paginator, Request $request, $cat)
     {
       //$crptRepo = new CryptocurrencyRepository();
       $query = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findByCategory($cat);

       dump($cat);

       $pagination = $paginator->paginate(
           $query, /* query NOT result */
           $request->query->getInt('page', 1), /*page number*/
           50 /*limit per page*/
       );

       return $this->render('cryptocurrency/list.html.twig', [
           'pagination' => $pagination,
       ]);
     }

     /**
      * @Route("/{id}", name="app_crpt_show", methods={"GET","POST"})
      */
     public function show(Cryptocurrency $crpt, $id): Response
     {
         $comments = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('id' => $id))->getCrptComments();
         return $this->render('cryptocurrency/show.html.twig', [
             'crpt' => $crpt,
             'comments' => $comments,
         ]);
     }

}
