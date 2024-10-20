<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request  $request): Response
    {

        /* // aller chercher les noms des dossiers dans public/photos
        $dossiers = scandir('photos');

        // on enlève les deux premiers éléments du tableau
        $dossiers = array_slice($dossiers, 2); */

        $finder = new Finder();
        $dossier = $finder->directories()->in('photos');

        // on va ajouter un formulaire
        $form = $this->CreateFormBuilder()
            ->add('dossier', TextType::class, ['label' => 'Nom du dossier'])
            ->add("ajouter", SubmitType::class, ['label' => 'Ajouter un dossier'])
            ->getForm();

        // gestion POST
        $form->handleRequest($request); // on regarde si le formulaire a été soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dossier = $data['dossier'];
            $fs = new Filesystem();
            $fs->mkdir("photos/$dossier");

            return $this->redirectToRoute('app_chatons', ['dossier' => $dossier]); // on redirige vers la page chatons
        }

        return $this->render('home/index.html.twig', [
            'dossiers' => $dossier,
            'formulaire' => $form->createView()
        ]);
    }

    #[Route("/chatons/{dossier}", name: 'app_chatons')]
    public function chatons($dossier, Request $request): Response
    {
        $fs = new Filesystem();
        $chemin = "photos/$dossier";
        if (!$fs->exists($chemin)) {
            throw $this->createNotFoundException("Le dossier $dossier n'existe pas");
        }

        $form = $this->CreateFormBuilder()
            ->add('photo', FileType::class, ['label' => 'Nom de la photo'])
            ->add("ajouter", SubmitType::class, ['label' => 'Ajouter une photo'])
            ->getForm();
        
        $fs = new Filesystem();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $photo = $data['photo'];
            $nomDeBase = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            foreach (scandir($chemin) as $fichier) {
                if ($fichier == $photo->getClientOriginalName()) {
                    $i = 1;
                    $newname = $nomDeBase . '_' . $i . '.' . $photo->getClientOriginalExtension();
            
                    while (file_exists($chemin . '/' . $newname)) {
                        $i++;
                        $newname = $nomDeBase . '_' . $i . '.' . $photo->getClientOriginalExtension();
                    }
                }
            }            

            $photo->move($chemin, $newname);
            return $this->redirectToRoute('app_chatons', ['dossier' => $dossier]);
        }

        $removeForm = $this->CreateFormBuilder()
            ->add('txtsupprimer', ChoiceType::class, [
                'choices' => array_flip(array_diff(scandir($chemin), array('.', '..')))
            ])
            ->add("supprimer", SubmitType::class, ['label' => 'Supprimer la photo'])
            ->getForm();
        $removeForm->handleRequest($request);
        if ($removeForm->isSubmitted() && $removeForm->isValid()) {
            $data = $removeForm->getData();
            $photo = $data['txtsupprimer'];
            $this->addFlash($photo, 'La photo a bien été supprimée');
            $fs->remove($chemin . '/' . $photo);
            return $this->redirectToRoute('app_chatons', ['dossier' => $dossier]);
        }

        $finder = new Finder();
        $photos = $finder->files()->in($chemin);

        return $this->render('home/liste_chatons.html.twig', [
            'nom_dossier' => $dossier,
            'photos' => $photos,
            'formulaire' => $form->createView(),
            'removeForm' => $removeForm->createView()
        ]);
    }
}   