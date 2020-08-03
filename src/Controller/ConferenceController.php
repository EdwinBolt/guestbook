<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/", name="conference")
     */
    public function index(Request $request){
        $greet = '';
        if ($name = $request -> query -> get('hello')){
            $greet = sprintf('<h1>Hello %s!</h1>', htmlspecialchars($name));
        }
        return new Response(<<<EOF
<html>
<body>
$greet
<img src="/images/construction.png" />
</body>
</html>
EOF
        );
    }
//    public function index()
//    {
////        return $this->render('conference/index.html.twig', [
////            'controller_name' => 'ConferenceController',
////        ]);
//        return new Response(<<<EOF
//<html>
//<body>
//<img src="/images/construction.png" />
//</body>
//</html>
//EOF
//        );
//    }
}
