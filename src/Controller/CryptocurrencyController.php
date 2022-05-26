<?php

namespace App\Controller;

use App\Entity\Cryptocurrency;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CryptocurrencyRepository;
use Symfony\Component\Security\Core\Security;
use Doctrine\Persistence\ObjectManager;

/**
 * @Route("/{_locale}")
 */
class CryptocurrencyController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
      $this->security = $security;
    }

    /**
     * @Route("/cryptocurrency", name="app_cryptocurrency")
     */
     public function listAction(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
     {
       //$query = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findAll();

         $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

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
             'cats_Name' => $categories,
         ]);
    }

    /**
     * @Route("/category", name = "app_category")
     */
     public function listCategory(PaginatorInterface $paginator, Request $request)
     {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
       //$crptRepo = new CryptocurrencyRepository();
       $query = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findByCategory($_POST['cat']);

       $pagination = $paginator->paginate(
           $query, /* query NOT result */
           $request->query->getInt('page', 1), /*page number*/
           50 /*limit per page*/
       );

       return $this->render('cryptocurrency/list.html.twig', [
           'pagination' => $pagination,
           'cats_Name' => $categories,
       ]);
     }

     /**
     * @Route("/price", name = "app_price")
     */
     public function searchPrice(PaginatorInterface $paginator, Request $request)
     {

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
         dump($_POST['Min']);
         dump ($_POST['Max']);
       //$crptRepo = new CryptocurrencyRepository();
       $query = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findPrice($_POST['Max'], $_POST['Min']);

       dump($query);

       $pagination = $paginator->paginate(
           $query, /* query NOT result */
           $request->query->getInt('page', 1), /*page number*/
           50 /*limit per page*/
       );

       return $this->render('cryptocurrency/list.html.twig', [
           'pagination' => $pagination,
           'cats_Name' => $categories,
       ]);
     }

    /**
     * @Route("/followers", name = "app_followers")
     */
    public function searchFollowers(PaginatorInterface $paginator, Request $request)
    {

       $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        dump($_POST['min']);
        dump ($_POST['max']);
      //$crptRepo = new CryptocurrencyRepository();
      $query = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findFollowers($_POST['max'], $_POST['min']);

      dump($query);

      $pagination = $paginator->paginate(
          $query, /* query NOT result */
          $request->query->getInt('page', 1), /*page number*/
          50 /*limit per page*/
      );

      return $this->render('cryptocurrency/list.html.twig', [
          'pagination' => $pagination,
          'cats_Name' => $categories,
      ]);
    }

     /**
      * @Route("/cryptocurrency", name = "app_namesearch")
      */
      public function listName(PaginatorInterface $paginator, Request $request)
      {
        //$crptRepo = new CryptocurrencyRepository();
        $query = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('crpt_Name' => $_POST['name']));

        dump($name);

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
      * @Route("/cryptocurrency/{id}", name="app_crpt_show", methods={"GET","POST"})
      */
     public function show(Cryptocurrency $crpt, $id): Response
     {
         $comments = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('id' => $id))->getCrptComments();
         return $this->render('cryptocurrency/show.html.twig', [
             'crpt' => $crpt,
             'comments' => $comments,
         ]);
     }


     ////////////////////////////////////////////
     ///////////Gestion des favoris/////////////
     //////////////////////////////////////////


     /**
      * @Route("/fav/{id}", name="app_crpt_fav", methods={"GET","POST"})
      */
     public function addfavourite($id): Response
     {
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->persist($this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('id' => $id))->addCrptFan($this->security->getUser()));
       $entityManager->flush();

       return $this->redirectToRoute('app_cryptocurrency', [], Response::HTTP_SEE_OTHER);
     }

     /**
      * @Route("unfav/{id}", name="app_crpt_unfav", methods={"GET","POST"})
      */
     public function remfavourite($id): Response
     {
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->persist($this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('id' => $id))->removeCrptFan($this->security->getUser()));
       $entityManager->flush();

       return $this->redirectToRoute('app_crpt_showfav', [], Response::HTTP_SEE_OTHER);
     }


     /**
      * @Route("/bookmark/", name="app_crpt_showfav", methods={"GET"})
      */
     public function showFav(PaginatorInterface $paginator, Request $request): Response
     {
       $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();;

       $query = $this->security->getUser()->getUserFavourites();
       dump($this->security->getUser());

       $pagination = $paginator->paginate(
           $query, /* query NOT result */
           $request->query->getInt('page', 1), /*page number*/
           50 /*limit per page*/
       );

       // parameters to template
       return $this->render('favoris/index.html.twig', [
           'pagination' => $pagination,
           'cats_Name' => $categories,
       ]);
     }
}
