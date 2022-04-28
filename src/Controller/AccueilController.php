<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ServiceAccueil;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil_index')]
    public function indexAction(ServiceAccueil $msgGen): Response
    {
        $msg = $msgGen->getMsg();
        $this->addFlash('info',$msg);
        return $this->render("Accueil/index.html.twig");
    }
}
