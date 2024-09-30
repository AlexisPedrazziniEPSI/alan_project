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
        #$dossier = scandir('public/photos');
        #$dossier = array_slice($dossier, 2);

        $finder = new Finder();
        $dossier = $finder->directories()->in('photos');

        return $this->render('home/index.html.twig', [
            'dossier' => $dossier,
        ]);
    }

    #[Route("/chatons/{NomDuDossier}", name: "app_liste_chat")]
    function listeChatons($NomDuDossier): Response
    {
        $fs = new Filesystem();
        $chemin = 'photos/' . $NomDuDossier;
        if (!$fs->exists($chemin)) {
            throw $this->createNotFoundException('Le dossier n\'existe pas');
        }

        $finder = new Finder();
        $images = $finder->files()->in($chemin);

        return $this->render('home/liste_chatons.html.twig', [
            'images' => $images,
            'NomDuDossier' => $NomDuDossier,
        ]);
    }
}
