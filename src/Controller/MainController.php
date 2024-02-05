<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// Déprécié :
// use Symfony\Component\Routing\Annotation\Route;
// A remplacer par :
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_index')]
    public function index(): Response
    {
        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
	
	#[Route('/test', name: 'app_admin_index')]
    #[IsGranted("ROLE_ADMIN")]
    public function test(): Response
    {
        return $this->render('main/test.html.twig', [
            'controller_name' => 'MainController:test',
        ]);
    }

    #[Route('/about-us', name: 'app_main_about_us')]
    public function aboutUs(): Response
    {
        $sJs = "<script>alert('bonjour');</script>";

        $aFruits = ['banane', 'fraise', 'cerises'];
        $aPersons = [ ['nom' => 'Loper', 'prenom' => 'Dave' ],
            ['nom' => 'Crosoft', 'prenom' => 'Mike' ]
        ];

//        $oDate = new \Datetime();
//        dump($oDate);
//        dd($oDate);

      return $this->render('main/about-us.html.twig',
        ['aFruits' => $aFruits,
            'aPersons' => $aPersons,
            'sJs' => $sJs]
      );
    }
}
