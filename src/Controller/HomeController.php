<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubCategoryRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{


    #[Route('/', name: 'app_home' ,  methods:['GET'])]
    public function index(ProductRepository $productRepository , CategoryRepository $categoryRepository): Response
    {

        $this->addFlash('danger','Ce produit est epuiser ');

        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories'=>$categoryRepository->findAll(),
        ]);
    }
    #[Route('home/proudct/{id}/show', name: 'app_home_product_show' ,  methods:['GET'])]

    public function show(Product $product , CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/show.html.twig', [
            'product' => $product,
            'categories'=>$categoryRepository->findAll(),

        ]);
    }
    #[Route('home/proudct/subcategory/{id}/filter', name: 'app_home_product_filter' ,  methods:['GET'])]
    public function filter($id,  SubCategoryRepository $SubCategoryRepository , CategoryRepository $categoryRepository): Response {
        $products = $SubCategoryRepository->find($id)->getProducts();
        $subCategory =  $SubCategoryRepository->find($id);
        return $this->render('home/filter.html.twig' , [
            'products'=> $products,
            'subCategory'=>$subCategory ,
            'categories'=>$categoryRepository->findAll(),
        ]);
    }

}
