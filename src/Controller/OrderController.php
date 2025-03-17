<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Entity\User;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


final class OrderController extends AbstractController
{


    #[Route('/order', name: 'app_order')]
    public function index(Request $request , 
    ProductRepository $productRepository , 
    SessionInterface $session , 
    EntityManagerInterface $entityManager , 
    Cart $cart , 
   
    ): Response
    {
        $data = $cart->getCart($session);

        $order = new Order();
        $form =$this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($order->isPayOnDelivery()) {
                if (!empty($data['total'])) {
                    $order->settotalPrice($data['total']);
                    $order->setCreateAt(new \DateTimeImmutable());
                    $entityManager->persist($order);
                    $entityManager->flush();

                    foreach ($data['cart'] as $value) {
                        $orderProduct = new OrderProducts();
                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($value['product']);
                    $orderProduct->setQte($value['quantity']);
                    $entityManager->persist($orderProduct);

                    // Décrémenter le stock du produit
                    $product = $value['product'];
                    $currentStock = $product->getStock();
                    $newStock = $currentStock - $value['quantity'];
                    $product->setStock($newStock);
                    $entityManager->persist($product);

                    }
                    $entityManager->flush();
                }
                $session->set('cart', []);
                return $this->redirectToRoute('app_order');
            }
        }
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total'=>$data['total']
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name:'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response{
        $cityShippingPrice = $city->getShippingCost();
        return new Response(json_encode(['status'=>200,'message'=>'on','content'=>$cityShippingPrice]));
    }

    #[Route('/editor/order', name: 'app_orders_show')]
    public function getAllOrder(OrderRepository $orderRepository, Request $request , SecurityController $security): Response
    {
        $user = $security->getUser(); 
        $searchId = $request->query->get('search_id');
        $orders = [];
    
        if ($searchId) {
            // Rechercher une commande par ID
            $order = $orderRepository->find($searchId);
            if ($order) {
                $orders = [$order];
            } else {
                $this->addFlash('danger', 'Aucune commande trouvée avec cet ID.');
            }
        } else {
            // Si aucun ID n'est spécifié, afficher toutes les commandes
            $orders = $orderRepository->findAll();
        }
    
        return $this->render('Order/order.html.twig', [
            "orders" => $orders,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_order_show', methods: ['GET'])]
    public function showOrder(int $id, OrderRepository $orderRepository): Response
{
    $order = $orderRepository->find($id);

  
    return $this->render('Order/show.html.twig', [
        'order' => $order,
    ]);
}

    #[Route("editor/order/{id}/is-completed/update", name:"app_orders_is_completed_update")]
    public function isCompletedUpdate($id , OrderRepository $orderRepository , EntityManagerInterface $entityManager): Response {
        $order = $orderRepository->find($id);
        $order->setIsCompleted(true);
        $entityManager->flush();
        $this->addFlash("success","Modification effectuée");
        return $this->redirectToRoute("app_orders_show");
    }

    #[Route("editor/order/{id}/remove", name:"app_orders_remove")] 
    public function removeOrder($id , Order $order , EntityManagerInterface $entityManager): Response {
       $entityManager->remove($order);
       $entityManager->flush();
       $this->addFlash("danger","Votre commande a été supprimer");
       return $this->redirectToRoute("app_orders_show");
    }



}
