<?php

namespace App\Controller;

use App\Document\Product;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(DocumentManager $dm): JsonResponse
    {
        $repository = $dm->getRepository(Product::class);
        $products = $repository->findAllOrderedByName();

        $results = [];
        foreach ($products as $product)
        {
            $results[] = $product->toString();
        }
        return new JsonResponse($results);
    }


    #[Route('/product/create', name: 'product_create')]

    public function create(DocumentManager $dm): JsonResponse
    {
        $strName =
            'reeselinda
            arroyozachary
            gonzalescatherine
            xcarpenter
            jcohen
            zsimmons
            stokestyler
            ofletcher
            monicaphelps
            johnharris';
        $names = explode("\n", $strName);
        $names =  array_map(function ($name)
        {
            $name = trim($name);
            if(!empty($name) && !is_null($name)) {
                return ($name);
            }
            }, $names);
        for($i= 0; $i< 100; $i++)
        {
            $random = array_rand($names,1);
            $product = new Product();
            $product->setName($names[$random]. ' '. rand(1,1000));
            $product->setPrice(rand(666,1000));

            $dm->persist($product);
        }

        $dm->flush();

        return new JsonResponse('Created product id ' . $product->getId());
    }


    #[Route('/product/{id}', name: 'product_show')]

    public function show(DocumentManager $dm, $id)
    {
        $product = $dm->getRepository(Product::class)->find($id);

        if (! $product) {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }


        $kq = (json_encode($product->getName()));
        dd($kq);
        return new JsonResponse($kq);

        // do something, like pass the $product object into a template
    }

}
