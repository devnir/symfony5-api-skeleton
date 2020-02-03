<?php

declare(strict_types=1);

namespace Api\ParamConverter\Config;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Annotation
 */
class MapperParamConverter extends ParamConverter
{
    /**
     * @var array
     */
    private $validationGroups = ['Default'];

    /**
     * @return array
     */
    public function getValidationGroups(): array
    {
        return $this->validationGroups;
    }

    /**
     * @param array $validationGroups
     *
     * @return $this
     */
    public function setValidationGroups(array $validationGroups): self
    {
        $this->validationGroups = $validationGroups;

        return $this;
    }
}
