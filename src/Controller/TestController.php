<?php

namespace App\Controller;

use App\Utility\Formular;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
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


    #[Route('/api/set_session', methods: ['GET'])]
    public function setSession(RequestStack $requestStack): JsonResponse
    {
        $session = $requestStack->getSession();
        $count =  $session->get('count');
       if(!$count) {

           // stores an attribute in the session for later reuse


           $count = 999;
           $session->set('count', $count);
           return $this->json([
               'message' => "Session did not set"
           ]);
       }
        #brew install php81-memcached
        #git clone  https://github.com/websupport-sk/pecl-memcache.git cd pecl-memcache
        #brew install php81-memcached --with-zlib-dir=/usr/local/Cellar/zlib/1.2.13/include/zlib.h

        return $this->json([
            'message' => "Count: {$count}"
        ]);

    }

}
