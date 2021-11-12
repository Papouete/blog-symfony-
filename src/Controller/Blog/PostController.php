<?php

namespace App\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Post;
use Datetime;

class PostController extends AbstractController
{
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repository->findAll();
        
        return $this->render('posts/index.html.twig', [
            'posts' => $posts    
        ]);
    }
    
    public function show(int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);
        $post = $repository->find($id);
        
        return $this->render('posts/show.html.twig', [
            'post' => $post    
        ]);
    }
    
    public function create(): Response
    {
        return $this->render('posts/create.html.twig');
    }
    
    public function store(): Response
    {
        $request = Request::createFromGlobals();
        
        $post = new Post();
        $post->setTitle($request->request->get('title'));
        $post->setContent($request->request->get('content'));
        $post->setCreatedAt(new DateTime());
        $post->setUpdatedAt(new DateTime());
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($post);
        $entityManager->flush();
        
        return $this->redirectToRoute('posts.index');
    }
}