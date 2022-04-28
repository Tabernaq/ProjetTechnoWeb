<?php

namespace App\Controller;

use App\Entity\UserV2;
use App\Form\UserV2EditType;
use App\Form\UserV2Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security ;

#[Route(path: '/security', name: 'security')]
class SecurityController extends AbstractController
{

    #[Route(path: '/login', name: '_login')]
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        /*if ($this->getUser()) {
             return $this->redirectToRoute('target_path');
        }*/

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
        $client = new UserV2();
        $client->setRoles(["ROLE_USER"]);
        $client->getPanier()->setClient($client);

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
        return $this->render('Client/client_creation.html.twig',$args);
    }

    #[Route('/compte/edit', name: '_compte_edit')]
    public function compteEditAction(EntityManagerInterface $em, Request $req,  UserPasswordHasherInterface $pswdHasher): Response
    {
        //On aimerait arriver à un formulaire qui permet ou non de changer de mot de passe sans avoir à le retaper à chaque fois, sans succès pour l'instant
        $clientRepo = $em->getRepository('App:UserV2');
        $id = $this->getUser()->getId();
        $client = $clientRepo->find($id);
        $oldPswd=$client->getPassword();

        $form = $this->createForm(UserV2EditType::class);
        $form->get("login")->setData($client->getUserIdentifier());
        $form->get("name")->setData($client->getName());
        $form->get("surname")->setData($client->getSurname());
        $form->get("date_birth")->setData($client->getDateBirth());
        $form->add('send', SubmitType::class, ['label' => 'Modifier votre compte client']);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid())
        {
            if($pswdHasher->hashPassword($client,$form->get("password")->getData())!=$pswdHasher->hashPassword($client,$oldPswd) && //Si le mot de passe actuel est correct
                $form->get("newPassword")->getData()!=null)//Si l'utilisateur rempli le champ "nouveau mot de passe"
                $client->setPassword($pswdHasher->hashPassword($client,$form->get("newPassword")->getData()));//On met à jour le mot de passe
            else
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
        return $this->render('Client/client_edit.html.twig',$args);
    }

    #[Route('/compte/delete/{id}',name: '_compte_delete', requirements: ["id" => "\d+"])]
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function compteDeleteAction(int $id, ManagerRegistry $doctrine): Response
    {   $em = $doctrine->getManager();
        $userRepo = $em->getRepository('App:UserV2');
        $target = $userRepo->find($id);
        if($this->getUser()==($target)){
            $this->addFlash('info', 'Vous ne pouvez pas supprimer un utilisateur connecté');
            return $this->render('Client/client_list.html.twig', array('users' => $userRepo->findAll()));
        }
        if($target->getRoles()->contains("ROLE_SUPERADMIN")){
            $this->addFlash('info', 'Vous ne pouvez pas supprimer un super-administrateur');
            return $this->render('Client/client_list.html.twig', array('users' => $userRepo->findAll()));
        }

        //Suppression du panier de l'utilisateur
        $panier = $em->getRepository("App:Panier")->findBy(array('client'=> $target->getId()))[0];//Le panier de l'utilisateur est unique, mais pour le retrouver on doit utiliser findBy qui renvoi un array
        $panierId =$panier->getId();
        $PGs = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $panierId));
        foreach ($PGs as $pg){
            $pg->getGoat()->addStock($pg->getQuantite());
            $em->remove($pg);
            $em->flush();
        }
        $em->remove($panier);

        $this->addFlash('info', 'L\'utilisateur a été supprimé');
        $em->remove($target);
        $em->flush();

        return $this->render('Client/client_list.html.twig', array('users' => $userRepo->findAll()));
    }


    #[Route('/gestion/utilisateur/list',name: '_gestion_utilisateur_list')]
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function gestionUtilisateurAction(ManagerRegistry $doctrine): Response
    {   $em = $doctrine->getManager();
        $userRepo = $em->getRepository('App:UserV2');
        $users = $userRepo->findAll();
        return $this->render('Client/client_list.html.twig', array('users' => $users));
    }


    #[Route('/gestion/admin/ajout',name: '_gestion_admin_ajout')]
    /**
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */
    public function gestionAdminAjoutAction(EntityManagerInterface $em, Request $req,  UserPasswordHasherInterface $pswdHasher): Response
    {
        $admin = new UserV2();
        $admin->setRoles(["ROLE_ADMIN"]);

        $form = $this->createForm(UserV2Type::class, $admin);
        $form->add('send', SubmitType::class, ['label' => 'Ajouter admin']);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid())
        {
            $admin->setPassword($pswdHasher->hashPassword($admin,$admin->getPassword()));
            $this->addFlash('Info', 'Création du compte admin réussie');
            $em->persist($admin);
            $em->flush();
            return $this->redirect($this->generateUrl('accueil_index'));
        }

        if ($form->isSubmitted())
            $this->addFlash('Info', 'Création incorrecte');
        //TODO : préciser quel champ pose probleme

        $args = array('myform' => $form->createView());
        return $this->render('Client/client_creation.html.twig',$args);
    }

}
