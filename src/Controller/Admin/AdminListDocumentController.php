<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Form\EditDocumentType;
use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
    * @Route("/admin/list", name="admin_list_")
    */
class AdminListDocumentController extends AbstractController
{
    /**
     * @Route("/document", name="document")
     */
    public function DocumentList(DocumentRepository $documents): Response
    {
        return $this->render('admin_list_document/document.html.twig', [
            'documents' => $documents->findAll(),
        ]);
    }

}
