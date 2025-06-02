<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationForm;
use App\Repository\FormateurRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\CommandeRepository;


#[Route('/formation')]
#[isgranted('ROLE_FORMATEUR')]
final class FormationController extends AbstractController
{

        #[Route('/stats', name: 'app_formation_stats', methods: ['GET'])]
        public function stats(
            FormationRepository $formationRepository,
            FormateurRepository $formateurRepository,
            CommandeRepository $commandeRepository
        ): Response
        {
            // 1️⃣ Get current formateur
            $formateur = $formateurRepository->findOneBy(['user' => $this->getUser()]);

            // 2️⃣ Get his formations
            $formations = $formationRepository->findBy(['formateur' => $formateur]);

            // 3️⃣ Get commandes (joined on panier → produitChoisi → formation)
            // Simple version first → get all commandes
            $commandes = $commandeRepository->findAll();

            // 4️⃣ KPIs
            $totalFormations = count($formations);
            $publishedCount = 0;
            foreach ($formations as $formation) {
                if ($formation->isPublished()) {
                    $publishedCount++;
                }
            }

            $totalRevenue = 0;
            $ventesParMois = [];

            foreach ($commandes as $commande) {
                if ($commande->getStatut()->value !== 'PAID') continue; // skip unpaid commandes

                $panier = $commande->getPanier();
                foreach ($panier->getProduits() as $produitChoisi) {
                    $produit = $produitChoisi->getProduit();

                    // Check if this produit is a Formation and belongs to current Formateur
                    if ($produit instanceof Formation && $produit->getFormateur() === $formateur) {
                        $totalRevenue += $produitChoisi->getPrix();

                        // Ventes par mois
                        $mois = $commande->getDateCommande()->format('Y-m');
                        if (!isset($ventesParMois[$mois])) {
                            $ventesParMois[$mois] = 0;
                        }
                        $ventesParMois[$mois]++;
                    }
                }
            }

            return $this->render('formation/stats.html.twig', [
                'totalFormations' => $totalFormations,
                'publishedCount' => $publishedCount,
                'totalRevenue' => $totalRevenue,
                'ventesParMois' => $ventesParMois,
            ]);
        }
    #[Route(name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository,FormateurRepository $formateurRepostiory): Response
    {
        $formateur=$formateurRepostiory->findOneBy(['user'=>$this->getUser()]);
        $formations=$formationRepository->findBy(['formateur'=>$formateur]);
        return $this->render('formation/index.html.twig', [
            'formations' =>$formations ,
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,FormateurRepository $formateurRepository): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationForm::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formateur=$formateurRepository->findOneBy(['user'=>$this->getUser()]);
            $formation->setFormateur($formateur);
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationForm::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }



}
