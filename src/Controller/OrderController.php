<?php

namespace App\Controller;

use App\Entity\OrderEntity;
use App\Entity\OrderDocument;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
final class OrderController extends AbstractController
{
    #[Route('', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $repo): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $repo->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $order = new OrderEntity();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($order);
            $em->flush();
            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(OrderEntity $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OrderEntity $order, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        return $this->render('order/edit.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
        ]);
    }

    #[Route('/document/{id}', name: 'app_order_delete_doc', methods: ['POST'])]
    public function deleteDoc(OrderDocument $doc, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $doc->getId(), $request->request->get('_token'))) {
            $orderId = $doc->getOrder()->getId();
            $em->remove($doc);
            $em->flush();
            return $this->redirectToRoute('app_order_show', ['id' => $orderId]);
        }

        return $this->redirectToRoute('app_order_index');
    }
}
