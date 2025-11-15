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
            $this->addFlash('success', 'Заказ создан');
            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/new.html.twig', [
            'form' => $form,
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
            $this->addFlash('success', 'Заказ обновлён');
            return $this->redirectToRoute('app_order_index');
        }

        return $this->render('order/edit.html.twig', [
            'form' => $form,
            'order' => $order,
        ]);
    }

    #[Route('/document/{id}', name: 'app_order_delete_document', methods: ['POST'])]
    public function deleteDocument(Request $request, OrderDocument $doc, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-document' . $doc->getId(), $request->request->get('_token'))) {
            $orderId = $doc->getOrder()->getId();
            $em->remove($doc);
            $em->flush();
            $this->addFlash('success', 'Файл удалён');
            return $this->redirectToRoute('app_order_edit', ['id' => $orderId]);
        }
        return $this->redirectToRoute('app_order_index');
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, OrderEntity $order, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $em->remove($order);
            $em->flush();
            $this->addFlash('success', 'Заказ удалён');
        }
        return $this->redirectToRoute('app_order_index');
    }
}
