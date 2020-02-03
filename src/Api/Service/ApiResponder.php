<?php


namespace Api\Service;


use Api\Dto\Request\PaginationDto;
use AutoMapperPlus\MapperInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiResponder
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var MapperInterface
     */
    private $mapper;


    public function __construct(SerializerInterface $serializer, PaginatorInterface $paginator, MapperInterface $mapper)
    {
        $this->serializer = $serializer;
        $this->paginator = $paginator;
        $this->mapper = $mapper;
    }

    /**
     * @param $data
     * @param string|null $dtoName
     * @param int $statusCode
     * @param array $context
     * @return JsonResponse
     *
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     */
    public function createResponse($data, ?string $dtoName = null, $statusCode = Response::HTTP_OK, array $context = []): JsonResponse
    {
        if (null !== $data) {
            if (null !== $dtoName) {
                $data = $this->mapper->map($data, $dtoName, $context);
            }
        }

        $context[DateTimeNormalizer::FORMAT_KEY] = 'Y-m-d h:i:s';
        if (null !== $data) {
            $data = $this->serializer->serialize($data, 'json', $context);
        }

        return new JsonResponse($data, $statusCode, [], true);
    }

    /**
     * @param $data
     * @param string|null $dtoName
     * @param int $statusCode
     * @param array $context
     * @return JsonResponse
     *
     * @throws \AutoMapperPlus\Exception\UnregisteredMappingException
     */
    public function createPaginatedResponse(PaginationDto $pagination, $data = null, ?string $dtoName = null, $statusCode = Response::HTTP_OK, array $context = []): JsonResponse
    {
        $paginationResult = $this->paginator->paginate($data, $pagination->page, $pagination->limit);
        $items = $paginationResult->getItems();
        if (null !== $dtoName) {
            $items = $this->mapper->mapMultiple($items, $dtoName, $context);
        }
        $data = [
            'data'  => $items,
            'meta'  => [
                'page'  => $paginationResult->getCurrentPageNumber(),
                'limit' => $paginationResult->getItemNumberPerPage(),
                'total' => $paginationResult->getTotalItemCount()
            ],
        ];

        return $this->createResponse($data, null, $statusCode, $context);
    }
}
