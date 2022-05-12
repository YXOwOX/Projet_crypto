<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class NavigationController extends AbstractController
{

   /**
    * @Route("/home", name="app_home")
    */
    public function home()
    {
        return $this->render('navigation/home.html.twig');
    }

    /**
     * @Route("/auth", name="app_auth")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function membre()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('navigation/auth.html.twig');
    }

    /**
     * @Route("/admin", name="app_admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin()
    {
        $user = $this->getUser();

        if($user && in_array('ROLE_ADMIN', $user->getRoles())){
               return $this->render('navigation/admin.html.twig');
       }

        return new RedirectResponse($urlGenerator->generate('app_login'));
    }
}
