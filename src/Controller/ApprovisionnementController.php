<?php

namespace App\Controller;

use App\Repository\ApprovisionnementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApprovisionnementController extends AbstractController
{
    /**
     * Affiche la liste des approvisionnements demandée par l'énoncé.
     * Le tri est fait du plus récent au plus ancien.
     */
    #[Route('/approvisionnements', name: 'app_approvisionnement_index')]
    public function index(ApprovisionnementRepository $approvisionnementRepository): Response
    {
        // Récupération des 5 approvisionnements, triés par date décroissante
        $approvisionnements = $approvisionnementRepository->findBy(
            [], 
            ['date' => 'DESC'] 
        );

        return $this->render('approvisionnement/index.html.twig', [
            'approvisionnements' => $approvisionnements,
        ]);
    }
}
