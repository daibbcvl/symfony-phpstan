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

        $mem_var = new \Memcached();
        $mem_var->addServer("127.0.0.1", 11211);
        $response = $mem_var->get("Bilbo");
        if ($response) {
           echo $response;
        } else {

            $mem_var->set("Bilbo", "Here s Your (Ring) Master stored in MemCached (^_^)") or die(" Keys Couldn't be Created : Bilbo Not Found :'( ");
        }


        $session = $requestStack->getSession();
        $count =  $session->get('count');
       if(!$count) {

           // stores an attribute in the session for later reuse


           $count = rand(100,999);
           $session->set('count', $count);
           return $this->json([
               'message' => "Session did not set"
           ]);
       }
        #brew install php81-memcached
        #git clone  https://github.com/websupport-sk/pecl-memcache.git cd pecl-memcache
        #brew install php81-memcached --with-zlib-dir=/usr/local/Cellar/zlib/1.2.13/include/zlib.h

        return $this->json([
            'message' => "Count: {$count}",
            'IP address' => $_SERVER['REMOTE_ADDR']
        ]);

    }

}
