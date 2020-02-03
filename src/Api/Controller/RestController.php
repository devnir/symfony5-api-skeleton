<?php


namespace Api\Controller;


use Api\Service\ApiResponder;
use AutoMapperPlus\AutoMapperInterface;

abstract class RestController
{
    /**
     * @var AutoMapperInterface
     */
    protected $mapper;

    /**
     * @var ApiResponder
     */
    protected $responder;

    /**
     * @required
     *
     * @param AutoMapperInterface $mapper
     */
    public function setMapper(AutoMapperInterface $mapper): void
    {
        $this->mapper = $mapper;
    }

    /**
     * @required
     *
     * @param ApiResponder $responder
     */
    public function setResponder(ApiResponder $responder): void
    {
        $this->responder = $responder;
    }
}
