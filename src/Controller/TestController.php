<?php

namespace App\Controller;

use App\Utility\Formular;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
        ]);
    }

    #[Route('/api/sum/{a}/{b}', methods: ['GET'])]
    public function sum(int $a, int $b): JsonResponse
    {
        return $this->json([
            'message' => "sum of {$a} + {$b} =" . Formular::sum($a, $b)
        ]);
    }

}
