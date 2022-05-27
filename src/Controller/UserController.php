<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/{_locale}")
 */
class UserController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @Route("/admin/user/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
      $user = $this->security->getUser();
      $id = $this->security->getUser()->getId();
      $comments = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('id' => $id))->getUserComments();

      if($user && (in_array('ROLE_ADMIN', $user->getRoles()))) {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
      }
      else {
        return $this->render('user/show.html.twig', [
            'comments' => $comments,
            'user' => $user,
        ]);
      }

    }

    /**
     * @Route("/user/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
              $entityManager = $this->getDoctrine()->getManager();
              //encodage du mot de passe
              $user->setUserPassword(
              $passwordEncoder->encodePassword($user, $user->getPassword()));
              $entityManager->persist($user);
              $entityManager->flush();

              if(in_array('ROLE_ADMIN', $user->getRoles()))
              {
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
              }
              else
              {
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
              }
        }

        $user = $this->security->getUser();

        if($user && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_USER', $user->getRoles()) )){
          return $this->render('user/new.html.twig', [
              'user' => $user,
              'form' => $form->createView(),
          ]);;
        }
        else {
          return $this->render('user/new1.html.twig', [
              'user' => $user,
              'form' => $form->createView(),
          ]);;
        }

    }

    /**
     * @Route("/auth/user/{id}", name="app_user_show", methods={"GET","POST"})
     */
    public function show(User $user, $id): Response
    {
        $comments = $this->getDoctrine()->getRepository(User::class)->findOneBy(array('id' => $id))->getUserComments();

        return $this->render('user/show.html.twig', [
            'comments' => $comments,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/auth/user/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
      $form = $this->createForm(User1Type::class, $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
              $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
              $this->getDoctrine()->getManager()->flush();

              return $this->redirectToRoute('utilisateur_index');
      }

      return $this->render('user/edit.html.twig', [
      'user' => $user,
      'form' => $form->createView(),
      ]);
    }

    /**
     * @Route("/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

}
