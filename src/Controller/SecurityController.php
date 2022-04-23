<?php

namespace App\Controller;

use App\Entity\UserV2;
use App\Form\UserV2Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(path: '/security', name: 'security')]
class SecurityController extends AbstractController
{

    #[Route(path: '/login', name: '_login')]
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: '_logout')]
    public function logoutAction(): void
    {}

    #[Route(path: '/logout_msg', name: '_logout_msg')]
    public function logoutMsgAction(): Response
    {
        $this->addFlash('info', 'Vous avez été déconnecté avec succès');
        return new RedirectResponse($this->generateURL('accueil_index'));
    }

    #[Route('/compte/creation', name: '_compte_creation')]
    public function compteCreationAction(EntityManagerInterface $em, Request $req,  UserPasswordHasherInterface $pswdHasher): Response
    {
        $ClientRepo = $em->getRepository('App:UserV2');
        $client = new UserV2();
        $client->setRoles(["ROLE_USER"]);

        $form = $this->createForm(UserV2Type::class, $client);
        $form->add('send', SubmitType::class, ['label' => 'Créer votre compte client']);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid())
        {
            $client->setPassword($pswdHasher->hashPassword($client,$client->getPassword()));
            $this->addFlash('Info', 'Création du compte client réussie');
            $em->persist($client);
            $em->flush();
            return $this->redirect($this->generateUrl('accueil_index'));
        }

        if ($form->isSubmitted())
            $this->addFlash('Info', 'Création incorrecte');
            //TODO : préciser quel champ pose probleme

        $args = array('myform' => $form->createView());
        return $this->render('Client/creation.html.twig',$args);
    }

    #[Route('/compte/edit', name: '_compte_edit')]
    public function compteEditAction(EntityManagerInterface $em, Request $req,  UserPasswordHasherInterface $pswdHasher): Response
    {
        $clientRepo = $em->getRepository('App:Film');
        $id = $this->getUser()->getUserIdentifier();
        $client = $clientRepo->find($id);

        $form = $this->createForm(UserV2Type::class, $client);
        $form->add('send', SubmitType::class, ['label' => 'Modifier votre compte client']);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid())
        {
            $client->setPassword($pswdHasher->hashPassword($client,$client->getPassword()));
            $em->flush();
            $this->addFlash('Info', 'Édition du client réussie');
            if($this->isGranted('ROLE_SUPERADMIN'))
                return $this->redirect($this->generateUrl('accueil_index'));
            else
                return $this->redirectToRoute('goat_list');
        }

        if ($form->isSubmitted())
            $this->addFlash('Info', 'Édition incorrecte');

        $args = array('myform' => $form->createView());
        return $this->render('Client/edit.html.twig',$args);
    }

}
