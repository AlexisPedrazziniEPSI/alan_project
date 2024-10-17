<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        /* // aller chercher les noms des dossiers dans public/photos
        $dossiers = scandir('photos');

        // on enlève les deux premiers éléments du tableau
        $dossiers = array_slice($dossiers, 2); */

        $finder = new Finder();
        $dossier = $finder->directories()->in('photos');

        return $this->render('home/index.html.twig', [
            'dossiers' => $dossier
        ]);
    }

    #[Route("/chatons/{dossier}", name: 'app_chatons')]
    public function chatons($dossier): Response
    {
        $fs = new Filesystem();
        $chemin = "photos/$dossier";
        if (!$fs->exists($chemin)) {
            throw $this->createNotFoundException("Le dossier $dossier n'existe pas");
        }

        $finder = new Finder();
        $photos = $finder->files()->in($chemin);

        return $this->render('home/chatons.html.twig', [
            'nom_dossier' => $dossier,
            'photos' => $photos
        ]);
    }
}