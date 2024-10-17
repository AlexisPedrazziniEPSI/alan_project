<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MenuController extends AbstractController
{
    public function menu(): Response
    {
        $finder = new Finder();
        $dossiers = $finder->directories()->in('photos');

        return $this->render('menu/index.html.twig', [
            "dossiers" => $dossiers
        ]);
    }
}
