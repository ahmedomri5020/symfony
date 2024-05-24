<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BlogFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "attr"=>[
                    "class"=>"form-control",
                ],
                "required"=>false,
            ])
            ->add('description', TextareaType::class, [
                "attr"=>[
                    "class"=>"form-control mb-2",
                ],
                "required"=>false,
            ])
            ->add('image', FileType::class, [
                "required"=>false,
                "mapped"=>false,
                "attr" => [
                    "accept" => "image/*",
                ],
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $blog = $event->getData();

                // If no file is uploaded, set the image property to null
                if (!$form->get('image')->getData()) {
                    $blog->setImage(null);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
