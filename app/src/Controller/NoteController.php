<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/note')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'note_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(NoteRepository $noteRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $notes = $noteRepository->findAll();
        } else {
            $notes = $noteRepository->findBy(['user' => $user]);
        }

        return $this->render('note/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    #[Route('/new', name: 'note_new', methods: ['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setUser($this->getUser());
            $note->setCreatedAt(new \DateTimeImmutable());
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/new.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'note_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Note $note): Response
    {
        $this->denyAccessUnlessGranted('view', $note);

        return $this->render('note/show.html.twig', [
            'note' => $note,
        ]);
    }

    #[Route('/{id}/edit', name: 'note_edit', methods: ['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Note $note, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('edit', $note);

        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'note_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Note $note, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('edit', $note);

        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $em->remove($note);
            $em->flush();
        }

        return $this->redirectToRoute('note_index');
    }
}