<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {      
        $blog = new Blog();
        $blog->setTitle("Title One");
        $blog->setDescription("This is blog 1 description");
        $blog->setImage("https://plus.unsplash.com/premium_photo-1689596510275-4ccefe8291a1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxlZGl0b3JpYWwtZmVlZHwyfHx8ZW58MHx8fHx8&auto=format&fit=crop&w=500&q=60");
        $blog->addAuthor($this->getReference("author_1"));
        $blog->addAuthor($this->getReference("author_2"));
        $manager->persist($blog);
        
        $blog2 = new Blog();
        $blog2->setTitle("Title Two");
        $blog2->setDescription("This is blog 2 description");
        $blog2->setImage("https://images.unsplash.com/photo-1693040516624-a22f0906faab?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxlZGl0b3JpYWwtZmVlZHwzfHx8ZW58MHx8fHx8&auto=format&fit=crop&w=500&q=60");
        $blog->addAuthor($this->getReference("author_3"));
        $manager->persist($blog2);
       
        $manager->flush();
    }
}
