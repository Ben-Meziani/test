<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    /**
     * @Route("/user/document", name="app_document")
     */
    public function index(): Response
    {
        $userId = $this->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $queryDocsUser = $em->createQuery(
            '
            SELECT
                d.name, d.id
            FROM 
                App\Entity\Document d
            WHERE
                d.users = :userID    
            '
        )->setParameter('userID', $userId)
        ;
        $results = $queryDocsUser->getResult();
        ;
        return $this->render('document/index.html.twig', [
            'documents' => $results,
        ]);
    }
}
