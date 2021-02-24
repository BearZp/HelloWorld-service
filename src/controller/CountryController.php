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
        $start = microtime(true);

        $countries = $countryComponent->getAll();

        if ($countries->isEmpty()) {
            return new Response("No data found", Response::HTTP_NOT_FOUND,
                ['content-type' => 'text/plain']);
        }
        var_dump(microtime(true) - $start);

        return $this->json($countries->toArray());
    }
}