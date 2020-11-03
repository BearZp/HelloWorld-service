<?php declare(strict_types=1);

namespace App\controller;

use Lib\logger\LogReferenceTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: bearzp
 * Date: 17.09.20
 * Time: 17:39
 */
class DefaultController
{
    use LogReferenceTrait;

    /** @var ContainerInterface */
    protected $container;

    /**
     * DefaultController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $response = New Response('Hello World !!! @');

        return $response;
    }
}