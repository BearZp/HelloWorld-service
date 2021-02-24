<?php

namespace App\controller;

use App\component\CityComponent;
use App\ddo\CreateCityRequest;
use App\Entity\City;
use App\Repository\CityRepository;
use Lib\logger\LogReferenceTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    use LogReferenceTrait;

    /**
     * @Route("/cities", name="cities", methods={"GET","HEAD"})
     * @param CityComponent $cityComponent
     * @return Response
     */
    public function index(CityComponent $cityComponent): Response
    {
        $start = microtime(true);

        $cities = $cityComponent->getCityList();

        if ($cities->isEmpty()) {
            return new Response("No data found", Response::HTTP_NOT_FOUND,
                ['content-type' => 'text/plain']);
        }
        var_dump(microtime(true) - $start);

        return $this->json($cities->toArray());
    }

    /**
     * @Route("/city", name="city", methods={"POST","PUT"})
     * @param Request $request
     * @param CityComponent $cityComponent
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Request $request, CityComponent $cityComponent): Response
    {
        $start = microtime(true);
        $post = json_decode($request->getContent());

        $createCityRequest = new CreateCityRequest();
        $createCityRequest->setName($post->name);
        $createCityRequest->setPopulation($post->population);
        try {
            $createCityRequest->validateModel();
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 400);
        }

        $city = $cityComponent->createCity($createCityRequest);

        var_dump(microtime(true) - $start);

        return $this->json($city);
    }
}