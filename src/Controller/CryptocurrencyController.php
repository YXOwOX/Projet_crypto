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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


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
         $variations = array();

         $pagination = $paginator->paginate(
             $query, /* query NOT result */
             $request->query->getInt('page', 1), /*page number*/
             50 /*limit per page*/
         );

         // parameters to template
         return $this->render('cryptocurrency/list.html.twig', [
             'pagination' => $pagination,
             'cats_Name' => $categories,
             'vars' => $variations,
         ]);
    }

    /**
     * @Route("/category/{cat}", name = "app_category")
     */
     public function listCategory(PaginatorInterface $paginator, Request $request, $cat)
     {
       $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
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
           'cats_Name' => $categories,
       ]);
     }


     /**
      * @Route("/cryptocurrency/name", name = "app_namesearch")
      */
      public function listName(PaginatorInterface $paginator, Request $request, UrlGeneratorInterface $urlGenerator) : Response
      {
          $crpt = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('crpt_Name' => $_POST['crpt_Name']));
          $idMoy = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('crpt_Name' => $_POST['crpt_Name']))->getCrptIdMoy();

          $url = "https://api.coingecko.com/api/v3/coins/".$idMoy."/ohlc?vs_currency=usd&days=365";
          $json = file_get_contents($url);
          $tabData = json_decode($json, true);

          $max = 0;
          $min = 0;

          foreach ($tabData as $day) {

              if($day[1] > $max){
                $max = $day[1];
              }

              if ($day[2] < $min) {
                $min = $day[2];
              }
            }

          if ($min == 0) {
           $min = 1;
          }

          $Moyenne = ($max + $min)/2;


          if ($crpt != null) {
            $comments = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('crpt_Name' => $_POST['crpt_Name']))->getCrptComments();

            return $this->render('cryptocurrency/show.html.twig', [
                'crpt' => $crpt,
                'comments' => $comments,
                'moy' => $Moyenne
            ]);
          }
          else {
            return new RedirectResponse($urlGenerator->generate('app_cryptocurrency'));
          }

      }


      /**
       * @Route("/cryptocurrency/average", name = "app_avgsearch")
       */
       public function listAverage(PaginatorInterface $paginator, Request $request, UrlGeneratorInterface $urlGenerator) : Response
       {
           $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
           $tab_crpt = array();
           $crpt = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findAll();

           dump($crpt);

           foreach ($crpt as $key => $crypto) {
             $url = "https://api.coingecko.com/api/v3/coins/".$crypto->getCrptIdMoy()."/ohlc?vs_currency=usd&days=365";
             $json = file_get_contents($url);
             $tabData = json_decode($json, true);

             $max = 0;
             $min = 0;

             foreach ($tabData as $day) {

                 if($day[1] > $max){
                   $max = $day[1];
                 }

                 if ($day[2] < $min) {
                   $min = $day[2];
                 }
               }

             if ($min == 0) {
              $min = 1;
             }

             $Moyenne = ($max + $min)/2;

             if ($crypto->getCrptPrice() > $Moyenne) {
               array_push($tab_crpt, $crypto);
             }
           }

           $pagination = $paginator->paginate(
               $tab_crpt, /* query NOT result */
               $request->query->getInt('page', 1), /*page number*/
               50 /*limit per page*/
           );

           return $this->render('cryptocurrency/list.html.twig', [
               'pagination' => $pagination,
               'cats_Name' => $categories,
           ]);

       }



     /**
      * @Route("/cryptocurrency/{id}", name="app_crpt_show", methods={"GET","POST"})
      */
     public function show(Cryptocurrency $crpt, $id): Response
     {

         $idMoy = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('id' => $id))->getCrptIdMoy();

         $url = "https://api.coingecko.com/api/v3/coins/".$idMoy."/ohlc?vs_currency=usd&days=365";
         $json = file_get_contents($url);
         $tabData = json_decode($json, true);

         $max = 0;
         $min = 0;

         foreach ($tabData as $day) {

             if($day[1] > $max){
               $max = $day[1];
             }

             if ($day[2] < $min) {
               $min = $day[2];
             }
          }

        $Moyenne = ($max + $min)/2;


        $comments = $this->getDoctrine()->getRepository(Cryptocurrency::class)->findOneBy(array('id' => $id))->getCrptComments();
         return $this->render('cryptocurrency/show.html.twig', [
             'crpt' => $crpt,
             'comments' => $comments,
             'moy' => $Moyenne
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
