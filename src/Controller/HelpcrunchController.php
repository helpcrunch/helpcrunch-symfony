<?php

namespace Helpcrunch\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Helpcrunch\Annotation\AuthSpecification\AutoLoginAuthSpecification;
use Helpcrunch\Annotation\AuthSpecification\DeviceAuthSpecification;
use Helpcrunch\Annotation\AuthSpecification\UserAuthSpecification;
use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Helper\ParametersValidatorHelper;
use Helpcrunch\Repository\HelpcrunchRepository;
use Helpcrunch\Response\EntitiesBatchResponse;
use Helpcrunch\Response\EntityNotFoundResponse;
use Helpcrunch\Response\EntityResponse;
use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Response\InnerErrorCodes;
use Helpcrunch\Response\SuccessResponse;
use Helpcrunch\Response\ValidationErrorResponse;
use Helpcrunch\Service\RedisService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Helpcrunch\Traits\CommandRunnerTrait;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Helpcrunch\Validator\Validator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class HelpcrunchController extends FOSRestController implements ClassResourceInterface
{
    use HelpcrunchServicesTrait, CommandRunnerTrait;

    const DEFAULT_PAGINATION_LIMIT = 50;

    /**
     * @var string
     */
    public static $entityClassName;

    /**
     * @var array
     */
    public static $unauthorizedMethods = [];

    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RedisService
     */
    protected $redis;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->entityManager = $this->getEntityManager();

        $this->redis = $this->getRedisService();
        $this->redis->connect();
    }

    /**
     * @UserAuthSpecification()
     * @DeviceAuthSpecification()
     * @AutoLoginAuthSpecification()
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cgetAction(Request $request): JsonResponse
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', static::DEFAULT_PAGINATION_LIMIT);

        return new EntitiesBatchResponse($this->getRepository()->findEntities($offset, $limit));
    }

    /**
     * @UserAuthSpecification()
     * @DeviceAuthSpecification()
     * @AutoLoginAuthSpecification()
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getAction($id): JsonResponse
    {
        if (!ParametersValidatorHelper::isValidId($id)) {
            return new ErrorResponse('Invalid ID', InnerErrorCodes::INVALID_ENTITY_ID);
        }
        if (!($entity = $this->getRepository()->find($id))) {
            return new EntityNotFoundResponse(static::getEntityName());
        }

        return new EntityResponse($entity);
    }

    /**
     * @UserAuthSpecification()
     * @DeviceAuthSpecification()
     * @AutoLoginAuthSpecification()
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function postAction(Request $request): JsonResponse
    {
        $entity = new static::$entityClassName;

        $validator = new Validator($this->container);
        if (!($entity = $validator->isValid($entity, $request->request->all()))) {
            return new ValidationErrorResponse(
                $validator->getErrors(),
                InnerErrorCodes::POST_ENTITY_VALIDATION_FAILED,
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // Stupid way to fix unique value duplicate
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            return new ErrorResponse('Value is already in use', InnerErrorCodes::INVALID_PARAMETER);
        }

        return new EntityResponse($this->getRepository()->find($entity->id), 'entity created', Response::HTTP_CREATED);
    }

    /**
     * @UserAuthSpecification()
     * @DeviceAuthSpecification()
     * @AutoLoginAuthSpecification()
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function putAction(Request $request, $id): JsonResponse
    {
        $result = $this->updateEntity($request->request->all(), $id);
        if ($result instanceof JsonResponse) {
            return $result;
        }

        return new EntityResponse($result, 'entity updated');
    }

    /**
     * @param array $data
     * @param int $id
     * @return HelpcrunchEntity|EntityNotFoundResponse|ErrorResponse
     */
    protected function updateEntity(array $data, $id)
    {
        if (!ParametersValidatorHelper::isValidId($id)) {
            return new ErrorResponse('Invalid ID', InnerErrorCodes::INVALID_ENTITY_ID);
        }
        if (!($entity = $this->getRepository()->find($id))) {
            return new EntityNotFoundResponse(static::getEntityName());
        }

        $validator = new Validator($this->container);
        if (!($entity = $validator->isValid($entity, $data))) {
            return new ValidationErrorResponse(
                $validator->getErrors(),
                InnerErrorCodes::POST_ENTITY_VALIDATION_FAILED,
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @UserAuthSpecification()
     * @DeviceAuthSpecification()
     * @AutoLoginAuthSpecification()
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction($id): JsonResponse
    {
        if (!ParametersValidatorHelper::isValidId($id)) {
            return new ErrorResponse('Invalid ID', InnerErrorCodes::INVALID_ENTITY_ID);
        }
        if (!($entity = $this->getRepository()->find($id))) {
            return new EntityNotFoundResponse(static::getEntityName());
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new SuccessResponse([], 'entity deleted');
    }

    /**
     * @return HelpcrunchRepository|ObjectRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getRepository(static::$entityClassName);
    }

    protected function getNewEntity(): HelpcrunchEntity
    {
        return new static::$entityClassName;
    }

    protected static function getEntityName(): string
    {
        $entityClassParts = explode('\\', static::$entityClassName);

        return end($entityClassParts);
    }
}
