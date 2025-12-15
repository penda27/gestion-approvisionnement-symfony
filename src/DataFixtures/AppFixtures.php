<?php
// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Fournisseur;
use App\Entity\Approvisionnement;
use App\Entity\LigneApprovisionnement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;

/**
 * Fixtures pour alimenter la base de données.
 * Les données correspondent aux 5 lignes d'approvisionnement de la maquette.
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- 1. CRÉATION DES FOURNISSEURS ---
        // On crée les fournisseurs mentionnés dans le tableau et dans la zone Statistique.
        $textilesDakar = new Fournisseur();
        $textilesDakar->setNom('Textiles Dakar SARL');
        $textilesDakar->setAdresse('Dakar, Zone Franche');
        $manager->persist($textilesDakar);

        $mercerieCentrale = new Fournisseur();
        $mercerieCentrale->setNom('Mercerie Centrale');
        $mercerieCentrale->setAdresse('Rue X, Marché');
        $manager->persist($mercerieCentrale);

        // Le 3e fournisseur pour l'APP-2023-003
        $tissusPremium = new Fournisseur();
        $tissusPremium->setNom('Tissus Premium');
        $tissusPremium->setAdresse('Local B, Quartier Y');
        $manager->persist($tissusPremium);


        // --- 2. CRÉATION DE QUELQUES ARTICLES ---
        // Articles de base pour alimenter les LignesApprovisionnement
        $articleA = (new Article())->setNom('Tissu Coton Bleu')->setReference('ART-TIS-001')->setQteStock(1000);
        $articleB = (new Article())->setNom('Fil Bobine Noir')->setReference('ART-FIL-010')->setQteStock(1000);
        $articleC = (new Article())->setNom('Fermeture Eclair')->setReference('ART-FER-025')->setQteStock(1000);
        $articleD = (new Article())->setNom('Boutons Plastique')->setReference('ART-BOU-100')->setQteStock(1000);

        $manager->persist($articleA);
        $manager->persist($articleB);
        $manager->persist($articleC);
        $manager->persist($articleD);
        $manager->flush(); // Flush ici pour avoir les IDs avant les appros

        // Définition des données des approvisionnements
        $approvisionnementsData = [
            // APP-2023-001: 3 articles, 750 000 FCFA
            [
                'ref' => 'APP-2023-001', 'date' => '2023-04-15', 'fournisseur' => $textilesDakar, 'statut' => 'Reçu',
                'montantTotal' => 750000.0, 
                'lignes' => [
                    ['article' => $articleA, 'qte' => 100, 'pu' => 7000.0],
                    ['article' => $articleB, 'qte' => 500, 'pu' => 100.0],
                    ['article' => $articleC, 'qte' => 10, 'pu' => 4500.0], // 3 articles en tout
                ]
            ],
            // APP-2023-002: 5 articles, 320 000 FCFA
            [
                'ref' => 'APP-2023-002', 'date' => '2023-04-10', 'fournisseur' => $mercerieCentrale, 'statut' => 'Reçu',
                'montantTotal' => 320000.0,
                'lignes' => [
                    ['article' => $articleD, 'qte' => 1000, 'pu' => 300.0], // 5 articles en tout
                    ['article' => $articleA, 'qte' => 10, 'pu' => 2000.0], 
                    ['article' => $articleB, 'qte' => 50, 'pu' => 40.0],
                ]
            ],
            // APP-2023-003: 2 articles, 450 000 FCFA
            [
                'ref' => 'APP-2023-003', 'date' => '2023-04-05', 'fournisseur' => $tissusPremium, 'statut' => 'En attente', 
                'montantTotal' => 450000.0,
                'lignes' => [
                    ['article' => $articleB, 'qte' => 5000, 'pu' => 90.0], // 2 articles en tout
                ]
            ],
            // APP-2023-004: 4 articles, 680 000 FCFA
            [
                'ref' => 'APP-2023-004', 'date' => '2023-04-01', 'fournisseur' => $textilesDakar, 'statut' => 'Reçu',
                'montantTotal' => 680000.0,
                'lignes' => [
                    ['article' => $articleC, 'qte' => 10, 'pu' => 68000.0], // 4 articles en tout
                    ['article' => $articleD, 'qte' => 50, 'pu' => 1000.0],
                ]
            ],
            // APP-2023-005: 8 articles, 520 000 FCFA
            [
                'ref' => 'APP-2023-005', 'date' => '2023-03-25', 'fournisseur' => $mercerieCentrale, 'statut' => 'Reçu',
                'montantTotal' => 520000.0,
                'lignes' => [
                    ['article' => $articleD, 'qte' => 1000, 'pu' => 500.0], // 8 articles en tout
                ]
            ],
        ];


        // --- 3. CRÉATION DES APPROVISIONNEMENTS ET DES LIGNES ---
        foreach ($approvisionnementsData as $data) {
            $appro = new Approvisionnement();
            $appro->setReference($data['ref']);
            // Utilisation de DateTimeImmutable pour une bonne pratique L3
            $appro->setDate(new DateTimeImmutable($data['date'])); 
            $appro->setFournisseur($data['fournisseur']);
            $appro->setStatut($data['statut']);
            $appro->setMontantTotal($data['montantTotal']); // Montant exact de la maquette

            $manager->persist($appro);

            // Création des lignes d'approvisionnement associées
            foreach ($data['lignes'] as $ligneData) {
                $ligne = new LigneApprovisionnement();
                $ligne->setArticle($ligneData['article']);
                $ligne->setQuantite($ligneData['qte']);
                $ligne->setPrixUnitaire($ligneData['pu']);
                $ligne->setApprovisionnement($appro); // Lien bidirectionnel
                
                $manager->persist($ligne);
            }
        }

        // Envoi final des données à la base de données
        $manager->flush();
    }
}