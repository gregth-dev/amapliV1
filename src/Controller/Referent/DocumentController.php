<?php

namespace App\Controller\Referent;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Service\Pagination;
use App\Service\DocumentFile;
use App\Service\HtFileMaker;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller des fonctionnalités liées aux documents
 * 
 * @Route("/referent/document")
 */
class DocumentController extends AbstractController
{
    /**
     * Affiche la liste des documents disponibles
     * 
     * @Route("/liste/{page<\d+>?1}", name="referent_document_index", methods={"GET"})
     */
    public function index(Pagination $pagination, $page): Response
    {
        $pagination
            ->setEntityClass(Document::class)
            ->setCurrentPage($page);
        return $this->render('referent/document/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Affiche et traite le formulaire d'ajout d'un document
     * 
     * @Route("/nouveau", name="referent_document_new", methods={"GET","POST"})
     */
    public function new(Request $request, DocumentFile $documentFile): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('files')->getData();
            if ($documentFile->newFile($document, $file)) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($document);
                $entityManager->flush();
                $this->addFlash('success', 'Le document a été enregistré');
                return $this->redirectToRoute('referent_document_index');
            } else
                $this->addFlash('danger', "Ce format de document n'est pas accepté");
        }

        return $this->render('referent/document/new.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche le détail d'un document
     * 
     * @Route("/{id}/consulter", name="referent_document_show", methods={"GET"})
     */
    public function show(Document $document, HtFileMaker $htFileMaker): Response
    {
        return $this->render('referent/document/show.html.twig', [
            'document' => $document,
        ]);
    }

    /**
     * Affiche et traite le formulaire de mise à jour d'un document
     * 
     * @Route("/{id}/editer", name="referent_document_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Document $document, DocumentFile $documentFile): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('files')->getData();
            if ($documentFile->updateFile($document, $file)) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Le document a été mis à jour');
                return $this->redirectToRoute('referent_document_index');
            } else
                $this->addFlash('danger', "Ce format de document n'est pas accepté");
        }
        return $this->render('referent/document/edit.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime un document
     * 
     * @Route("/{id}", name="referent_document_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Document $document, DocumentFile $documentFile): Response
    {
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $documentFile->deleteFile($document);
            $entityManager->remove($document);
            $entityManager->flush();
            return new JsonResponse(['success' => 1, 'text' => 'Document supprimé']);
        } else {
            return new JsonResponse(['error' => 1, 'text' => "Erreur lors de la suppression du document"]);
        }
    }
}
