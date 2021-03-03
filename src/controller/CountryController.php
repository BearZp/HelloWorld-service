<?php


namespace App\controller;


use App\component\CountryComponent;
use Lib\logger\LogReferenceTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{
    use LogReferenceTrait;

    /**
     * @Route("/countries", name="countries", methods={"GET","HEAD"})
     * @param CountryComponent $countryComponent
     * @return Response
     */
    public function index(CountryComponent $countryComponent): Response
    {
//        $start = microtime(true);
//        $countries = $countryComponent->getAll();
//        if ($countries->isEmpty()) {
//            return new Response("No data found", Response::HTTP_NOT_FOUND,
//                ['content-type' => 'text/plain']);
//        }
//        $result[] = $countries->toArray();
//        var_dump(microtime(true) - $start);


        $start = microtime(true);
        $countries = $countryComponent->getAllById();
        if ($countries->isEmpty()) {
            return new Response("No data found", Response::HTTP_NOT_FOUND,
                ['content-type' => 'text/plain']);
        }
        $result[] = $countries->toArray();
        $sync = (microtime(true) - $start);


        $start = microtime(true);
        $countries = $countryComponent->getAllByIdAsync();
        if ($countries->isEmpty()) {
            return new Response("No data found", Response::HTTP_NOT_FOUND,
                ['content-type' => 'text/plain']);
        }
        $result[] = $countries->toArray();
        $async = (microtime(true) - $start);

        var_dump($sync);
        var_dump($async);
        var_dump('DELTA TIME:' . round(($sync - $async) * 1000, 2) . ' ms');

        return $this->json($result);
    }
}