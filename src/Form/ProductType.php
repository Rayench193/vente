<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image as ConstraintsImage;
use Symfony\Component\Validator\Constraints\ImageValidator;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('auteur')
            ->add('descreption')
            ->add('price')
            ->add('stock')
            ->add('image',FileType::class,[
                'label'=> 'Image de produit',
                'mapped'=>false,
                'required'=> false,
                "constraints"=>[
                    new File([
                        "maxSize"=>"500K",
                        "mimeTypes"=> [
                            'image/jpg',
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage'=> "votre image de produit doit éte au format valide (jpg,png,jpeg)"

                    ])
                ]
            ])                   
            ->add('subCategories', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
