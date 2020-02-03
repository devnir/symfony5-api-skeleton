<?php

declare(strict_types=1);

namespace Api\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * IndexController
 */
class IndexController
{
    /**
     * @Route(path="/", methods={"GET"}, name="index")
     *
     * @param Request $request
     * @param ParameterBagInterface $bag
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request, ParameterBagInterface $bag): JsonResponse
    {
        if ($bag->has('environment') && ('prod' !== $bag->get('environment')) && $request->query->has('getInfo')) {
            phpinfo();
        }
        return new JsonResponse(['service' => 'skeleton sf api']);
    }
}
