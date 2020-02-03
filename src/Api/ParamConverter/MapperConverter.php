<?php

declare(strict_types=1);

namespace Api\ParamConverter;

use Api\Exception\ValidationException;
use Api\ParamConverter\Config\MapperParamConverter;
use AutoMapperPlus\AutoMapperInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Map request to dto specified in controller
 */
class MapperConverter implements ParamConverterInterface
{
    /**
     * @var AutoMapperInterface
     */
    private $mapper;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param AutoMapperInterface    $mapper
     * @param ValidatorInterface $validator
     */
    public function __construct(AutoMapperInterface $mapper, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->mapper = $mapper;
    }

    /**
     * @param Request $request
     * @param ParamConverter|MapperParamConverter $configuration
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();
        $dto = $this
            ->mapper
            ->convert(
                \array_merge(
                    $request->attributes->get('_route_params', []),
                    $request->query->all(),
                    $request->request->all()
                ),
                $class
            );

        $errors = $this->validator->validate($dto, null, $configuration->getValidationGroups());
        if (count($errors) > 0) {
            throw new ValidationException(iterator_to_array($errors));
        }

        $request->attributes->set($configuration->getName(), $dto);

        return true;
    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration instanceof MapperParamConverter;
    }
}
