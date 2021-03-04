<?php


namespace App\controller;


use App\component\CityComponent;
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
     * @param CityComponent $cityComponent
     * @return Response
     */
    public function index(CountryComponent $countryComponent, CityComponent $cityComponent): Response
    {
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

        $start = microtime(true);
        $cities = $cityComponent->getAllById();
        if ($cities->isEmpty()) {
            return new Response("No data found", Response::HTTP_NOT_FOUND,
                ['content-type' => 'text/plain']);
        }
        $result[] = $cities->toArray();
        $em = (microtime(true) - $start);

        var_dump('Entities:     ' . $em);
        var_dump('Sync Custom:  ' . $sync);
        var_dump('Async Custom: ' . $async);

        var_dump('DELTA EM:   ' . round(($em - $async) * 1000, 2) . ' ms');
        var_dump('DELTA Sync: ' . round(($sync - $async) * 1000, 2) . ' ms');

        return $this->json($result);
    }
}