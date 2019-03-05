<?php

namespace Helpcrunch\Controller;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Helper\ParametersValidatorHelper;
use Helpcrunch\Repository\HelpcrunchRepository;
use Helpcrunch\Response\EntitiesBatchResponse;
use Helpcrunch\Response\EntityNotFoundResponse;
use Helpcrunch\Response\EntityResponse;
use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Response\InnerErrorCodes;
use Helpcrunch\Response\SuccessResponse;
use Helpcrunch\Service\RedisService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Helpcrunch\Validator\Validator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class HelpcrunchController extends FOSRestController implements ClassResourceInterface
{
    use HelpcrunchServicesTrait;

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

    public function cgetAction(Request $request): JsonResponse
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', self::DEFAULT_PAGINATION_LIMIT);

        return new EntitiesBatchResponse($this->getRepository()->findEntities($offset, $limit));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getAction($id)
    {
        if (!ParametersValidatorHelper::isValidId($id)) {
            return new ErrorResponse('Invalid ID', InnerErrorCodes::INVALID_ENTITY_ID);
        }
        if (!($entity = $this->getRepository()->find($id))) {
            return new EntityNotFoundResponse(self::$entityClassName);
        }

        return new EntityResponse($entity);
    }

    public function postAction(Request $request): JsonResponse
    {
        $entity = new static::$entityClassName;

        $validator = new Validator();
        if (!($entity = $validator->validate($entity, $request->request->all()))) {
            return new ErrorResponse(
                $validator->getErrors(),
                InnerErrorCodes::POST_ENTITY_VALIDATION_FAILED,
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new EntityResponse($this->getRepository()->find($entity->id), 'entity created', Response::HTTP_CREATED);
    }

    public function putAction(Request $request, $id): JsonResponse
    {
        $entityResponse = $this->getAction($id);
        if ($entityResponse instanceof EntityResponse) {
            $entity = $entityResponse->getEntity();
        } else {
            return $entityResponse;
        }

        $validator = new Validator();
        if (!($entity = $validator->validate($entity, $request->request->all()))) {
            return new ErrorResponse(
                $validator->getErrors(),
                InnerErrorCodes::POST_ENTITY_VALIDATION_FAILED,
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $this->entityManager->flush();

        return new EntityResponse($entity, 'entity updated');
    }

    public function deleteAction($id): JsonResponse
    {
        $entityResponse = $this->getAction($id);
        if ($entityResponse instanceof EntityResponse) {
            $entity = $entityResponse->getEntity();
        } else {
            return $entityResponse;
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

    protected function runCommand(KernelInterface $kernel, string $command, array $options): void
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $arguments = ['command' => $command];
        $arguments = array_merge($arguments, $options);

        $input = new ArrayInput($arguments);

        $output = new ConsoleOutput();
        $application->run($input, $output);
    }
}
