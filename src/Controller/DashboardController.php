<?php

namespace App\Controller;

use App\Repository\OrderProductsRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(UserRepository $userRepository , ProductRepository $productRepository , OrderRepository $orderRepository , OrderProductsRepository $orderProductsRepository): Response
    {
      $userCount = $userRepository->count([]);
      $productCount = $productRepository->count([]);
      $orderCount = $orderRepository->count([]);
      $mostPurchasedProducts = $orderProductsRepository->findMostPurchasedProducts();

      //preparer les données pour le graphique
      $productNames = [];
        $productQuantities = [];
        foreach ($mostPurchasedProducts as $product) {
            $productNames[] = $product['name'];
            $productQuantities[] = $product['totalQuantity'];
        }

    
      return $this->render('dashboard/index.html.twig', [
        'userCount' => $userCount,
        'productCount' => $productCount,
        'orderCount' => $orderCount,
        'mostPurchasedProducts' => $mostPurchasedProducts,
        'productNames' => $productNames,
        'productQuantities' => $productQuantities,
       
    ]);
    }
}
