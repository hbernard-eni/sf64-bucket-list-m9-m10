<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Import d'un service
use App\Util\Censurator;

#[Route('/wish')]
#[IsGranted("ROLE_USER")]
class WishController extends AbstractController
{
     // Avec injection de dépendances
     private Censurator $censurator;
     private EntityManagerInterface $em;

     // Ajout du constructeur pour l'injection de dépendances
     public function __construct(EntityManagerInterface $em, Censurator $censurator) {
    	$this->em = $em;
    	$this->censurator = $censurator;
    }

    #[Route('/', name: 'app_wish_index', methods: ['GET'])]
    public function index(WishRepository $wishRepository): Response
    {
        return $this->render('wish/index.html.twig', [
            'wishes' => $wishRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_wish_new', methods: ['GET', 'POST'])]
//    Sans injection de dépendances
//    public function new(Request $request, EntityManagerInterface $entityManager, Censurator $censurator): Response
////    Avec injection de dépendances
    public function new(Request $request): Response
    {
        $wish = new Wish();

        // Description par défaut avec texte à filtrer
        $description = 'Dave Loper is a very bad guy who eats bananas, plays casino and takes viagra.';
        $wish->setDescription($description);

        $wish->setTitle('Test');
        $wish->setAuthor('hbernard');
        $wish->setDateCreated(new \DateTime());

        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // Sans injection
//           $purifiedDescription = $censurator->purify($wish->getDescription());

            // Avec injection :
            $purifiedDescription = $this->censurator->purify($wish->getDescription());

            $wish->setDescription($purifiedDescription);

//            Sans injection de dépendances
//            $entityManager->persist($wish);
//            $entityManager->flush();

//            Avec injection de dépendances
            $this->em->persist($wish);
            $this->em->flush();

            return $this->redirectToRoute('app_wish_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('wish/new.html.twig', [
            'wish' => $wish,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_wish_show', methods: ['GET'])]
    public function show(Wish $wish): Response
    {
        return $this->render('wish/show.html.twig', [
            'wish' => $wish,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_wish_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Wish $wish, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WishType::class, $wish);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_wish_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('wish/edit.html.twig', [
            'wish' => $wish,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_wish_delete', methods: ['POST'])]
    public function delete(Request $request, Wish $wish, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$wish->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wish);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_wish_index', [], Response::HTTP_SEE_OTHER);
    }
}
