<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\User\Type\CreateAccountType;
use App\Form\User\Type\EditAccountType;
use App\Form\User\Type\UpdatePasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/user", name="front_user_")
 * @Template()
 */
class UserController extends AbstractController
{
    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     *
     * @return array|Response
     */
    public function new(Request $request)
    {
        $user = new User();
        $form = $this->createForm(CreateAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     *
     * @return array|Response
     */
    public function edit(Request $request, User $user)
    {
        $formEditAccount = $this->createForm(EditAccountType::class, $user);
        $formEditAccount->handleRequest($request);

        if ($formEditAccount->isSubmitted() && $formEditAccount->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('front_user_edit', [
                'id' => $user->getId(),
                '_fragment' => 'dddd',
            ]);
        }

        $formUpdatePassword = $this->createForm(UpdatePasswordType::class, $user);
        $formUpdatePassword->handleRequest($request);

        if ($formUpdatePassword->isSubmitted() && $formUpdatePassword->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('front_user_edit', [
                'id' => $user->getId(),
            ]);
        }

        return [
            'user' => $user,
            'formEditAccount' => $formEditAccount->createView(),
            'formUpdatePassword' => $formUpdatePassword->createView(),
        ];
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, FlashBagInterface $flashBag, User $user): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            /** @var TokenStorageInterface $tokenStorage */
            $tokenStorage = $this->get('security.token_storage');
            $tokenStorage->setToken(null);

            $flashBag->add('notice', 'Votre compte a bien été supprimé');

            return $this->redirectToRoute('app_logout');
        }

        return $this->redirectToRoute('home');
    }
}
