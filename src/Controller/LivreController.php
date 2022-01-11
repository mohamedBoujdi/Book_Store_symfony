<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use App\Repository\AuteurRepository;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'livre_index', methods: ['GET'])]
    public function index(LivreRepository $livreRepository,GenreRepository $genreRepository,AuteurRepository $auteurRepository): Response
    {
     return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
           'genres' => $genreRepository->findAll(),
           'auteurs'=> $auteurRepository->findAll(),
           'dates'=>   $livreRepository->findDates(),
           'keyword'=> null,
           'datemax'=> null,
           'datemin'=> null,
           'note'=>null,
            'datepub'=>null,
            'auteurch'=>null,
            'genrech'=>null,
        ]);
    }

    #[Route('/livre/new', name: 'livre_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/livre/{id}', name: 'livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/livre/{id}/edit', name: 'livre_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/livre/{id}', name: 'livre_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livre_index', [], Response::HTTP_SEE_OTHER);
    }

     #[Route("/liv/chercher", name:"livre_chercher", methods:["GET"])]
     
    public function chercher( Request $request , LivreRepository $livreRepository,GenreRepository $genreRepository,AuteurRepository $auteurRepository): Response
    {    
        $keyword=$request->query->get("k");
        $dmx=$request->query->get("dmx");
        $dmn=$request->query->get("dmn");
        $note=$request->query->get("n");
        $datepub=$request->query->get("p");
        $auteur=$request->query->get("a");
        $genre=$request->query->get("g");
        
        $livres = $livreRepository->findByCriter($keyword,$dmn,$dmx,$note,$datepub,$auteur,$genre);

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
            'genres' => $genreRepository->findAll(),
           'auteurs'=> $auteurRepository->findAll(),
           'dates'=>  $livreRepository->findDates(),
            'keyword'=> $keyword,
            'datemax'=>$dmx,
            'datemin'=> $dmn,
            'note'=>$note,
            'datepub'=>$datepub,
            'auteurch'=>$auteur,
            'genrech'=>$genre,
          
        ]);
    }

}
