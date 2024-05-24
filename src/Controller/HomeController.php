<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogFormType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class HomeController extends AbstractController
{
    private $blogRepository;
    private $em;
    public function __construct(BlogRepository $blogRepository, EntityManagerInterface $em){
        $this->blogRepository= $blogRepository;
        $this->em= $em;
    }
    

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
    
    #[Route('/blogs',methods:["GET"], name: 'blogs')]
    public function blogs(): Response
    {
        $blogs = $this->blogRepository->findAll();        
        return $this->render('blog/index.html.twig',
         ["blogs"=>$blogs]);
    }
    #[Route('/blogs/{id}',methods:["GET"], name: 'blogdetail')]
    public function blogdetail($id): Response
    {
        $blog = $this->blogRepository->find($id); 
        if($blog){
            return $this->render('blog/show.html.twig',
            ["blog"=>$blog]);
        }  
        else{
            return $this->redirectToRoute('blogs');
            
        }     
       
    }
    #[Route('/create', methods:["GET", "POST"], name: 'create')]
    public function create(Request $request): Response
    {       
        $blog= new Blog();
        $form = $this->createForm(BlogFormType::class, $blog); 
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $newBlog = $form->getData();
            $image = $form->get("image")->getData();
            
            if($image){
                $newImage = uniqid(). ".". $image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter("kernel.project_dir")."/public/uploads",
                        $newImage
                    );
                    $newBlog->setImage("/uploads/".$newImage);
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
            } else {
                // Handle the case when no image is provided
                $newBlog->setImage(null);
            }
            
            $this->em->persist($newBlog);
            $this->em->flush();
            
            return $this->redirectToRoute("blogs");
        }
    
        return $this->render('blog/create.html.twig', [ "form"=>$form->createView()]);       
    }
    #[Route('/edit/{id}', name: 'edit')]
    public function edit($id, Request $request): Response
    {
        $blog = $this->blogRepository->find($id); 
        if($blog){
            $form=$this->createForm(BlogFormType::class, $blog);
            $form->handleRequest($request);
            $image = $form->get("image")->getData();
            if($form->isSubmitted() &&$form->isValid()){
                if($image){
                    if($blog->getImage() !== ""){
                        if(file_exists($this->getParameter("kernel.project_dir").$blog->getImage())){
                           unlink($this->getParameter("kernel.project_dir").$blog->getImage());                       
                            $newImageName = uniqid(). ".".$image->guessExtension();
                            try {
                                $image->move(
                                    $this->getParameter("kernel.project_dir")."/public/uploads",
                                    $newImageName
                                );
                            } catch (FileException $e) {
                                return new Response($e->getMessage());
                            }

                            $blog -> setImage("/uploads/".$newImageName);
                            $this->em->flush();
                            return $this->redirectToRoute("blogs");
                        }else{
                            $newImageName = uniqid(). ".".$image->guessExtension();
                            try {
                                $image->move(
                                    $this->getParameter("kernel.project_dir")."/public/uploads",
                                    $newImageName
                                );
                            } catch (FileException $e) {
                                return new Response($e->getMessage());
                            }

                            $blog -> setImage("/uploads/".$newImageName);
                            $this->em->flush();
                            return $this->redirectToRoute("blogs");
                        }

                    }

                }else{
                    $blog->setTitle($form->get("title")->getData());
                    $blog->setDescription($form->get("description")->getData());
                    $this->em->flush();
                    return $this->redirectToRoute('blogs');
                }
            }

            return $this->render('blog/edit.html.twig',
            ["form"=>$form->createView()]);
        }  
        else{
            return $this->redirectToRoute('blogs');
            
        }   
    }

    #[Route('/delete/{id}',methods:["get", "delete"], name: 'delete')]
    public function delete($id): Response
    {
        $blog= $this->blogRepository->find($id);
        if($blog){
            $this->em->remove($blog);
            $this->em->flush();
            return $this->redirectToRoute('blogs');
        }  else{
            return $this->redirectToRoute('blogs');
        }     


    }

}
