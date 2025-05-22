<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        $posts = $this->em->getRepository(Post::class)->findAll();
        return $this->render('main/index.html.twig', [
            'posts' => $posts,
        ]);
    }

#[Route('/Create', name: 'Create')]
public function CreateData(Request $request): Response
{
    $post = new Post();
    $form = $this->createForm(PostForm::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->em->persist($post);
        $this->em->flush();

        $this->addFlash('success', 'Post saved successfully!');

        return $this->redirectToRoute('app_main'); // Redirect after successful submission
    }

    return $this->render('main/post.html.twig', [
        'form' => $form->createView(), // Corrected 'from' to 'form'
    ]);
}


    #[Route('/edit/{id}', name: 'post_edit')]
public function edit(int $id, Request $request): Response
{
    $post = $this->em->getRepository(Post::class)->find($id);

    if (!$post) {
        throw $this->createNotFoundException('Post not found');
    }

    $form = $this->createForm(PostForm::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->em->flush();  // No need to call persist
        $this->addFlash('success', 'Post updated!');
      return $this->redirectToRoute('app_main');
    }

    return $this->render('main/post.html.twig', [
        'form' => $form->createView(),
        'action' => 'Edit Post'
    ]);
}


#[Route('/delete/{id}', name: 'post_delete')]
public function delete(int $id, Request $request): Response
{
    $post = $this->em->getRepository(Post::class)->find($id);

    if (!$post) {
        throw $this->createNotFoundException('Post not found');
    }

    $this->em->remove($post);
    $this->em->flush();

    $this->addFlash('success', 'Post deleted!');
    return $this->redirectToRoute('app_main');
}




}
