<?php 

namespace App\Service;
use App\Repository\ProductRepository;

class Cart {
    public function __construct(private readonly ProductRepository $productRepository) {}

    public function getCart($session): array {
        $cart = $session->get('cart', []);
        $cartWithData = [];
        $total = 0;
        
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            
            // Only add to cart if product exists
            if ($product) {
                $cartWithData[] = [
                    "product" => $product,
                    "quantity" => $quantity,
                ];
                $total += $product->getPrice() * $quantity;
            } else {
                // Remove non-existent products from cart
                unset($cart[$id]);
                $session->set('cart', $cart);
            }
        }

        return [
            'cart' => $cartWithData,
            'total' => $total,
        ];
    }
}