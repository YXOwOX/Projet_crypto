<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(UrlGeneratorInterface $urlGenerator): Response
    {

        return new RedirectResponse($urlGenerator->generate('app_login'));

        // return $this->render('default/index.html.twig', [
        //     'controller_name' => 'DefaultController',
        // ]);
    }
}
