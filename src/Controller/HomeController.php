<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {

//        //Aller chercher le nom des dossiers dans le dossier public/photos
//        $dossiers = scandir('photos');
//
//        //on enlève les deux premiers éléments du tableau . et ..
//        $dossiers = array_slice($dossiers, 2);
        // au lieu du scandir, je vais utiliser un objet symfony
        $finder = new Finder();
        $dossiers = $finder->directories()->in('photos');

        //on envoie le tableau à la vue
        return $this->render('home/menu.html.twig', [
            'dossiers' => $dossiers
        ]);
    }

    #[Route('/chatons/{dossier}', name: 'app_chatons')]
    public function listechatons($dossier): Response
    {
        //On va vérifier que le dossier existe bien avec symfony
        $fs = new Filesystem();
        $chemin = "photos/$dossier";
        if(!$fs->exists($chemin)) {
            throw $this->createNotFoundException('Le dossier n\'existe pas');
        }
        //on va chercher les images dans le dossier
        $finder = new Finder();
        $images = $finder->files()->in($chemin);

        return $this->render('home/liste_chatons.html.twig', [
            'nom_dossier' => $dossier,
            'images' => $images,
//            'dossier' => $dossier
        ]);

    }
}
