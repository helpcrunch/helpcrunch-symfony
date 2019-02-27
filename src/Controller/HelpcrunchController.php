<?php

namespace Helpcrunch\Controller;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Repository\HelpcrunchRepository;
use Helpcrunch\Response\EntitiesBatchResponse;
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
use Helpcrunch\Traits\FormTrait;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

abstract class HelpcrunchController extends FOSRestController implements ClassResourceInterface
{
    use FormTrait, HelpcrunchServicesTrait;

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
     * @Rest(requirements= {"id": "\d+"})
     *
     * @param int $id
     * @return EntityResponse
     */
    public function getAction(int $id)
    {
        return new EntityResponse($this->findEntityById($id));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postAction(Request $request): JsonResponse
    {
        $entity = new static::$entityClassName;

        $form = $this->checkDataIsValid($request->request->all(), $this->createNewForm($entity));
        if (!$form['valid']) {
            return new ErrorResponse(
                'validation error(s)',
                InnerErrorCodes::POST_ENTITY_VALIDATION_FAILED,
                ErrorResponse::HTTP_BAD_REQUEST,
                $form['errors']
            );
        }

        $entity = $form['entity'];
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new EntityResponse($this->findEntityById($entity->id), 'entity created', Response::HTTP_CREATED);
    }

    /**
     * @Rest(requirements= {"id": "\d+"})
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function putAction(Request $request, int $id): JsonResponse
    {
        $entity = $this->findEntityById($id);
        $form = $this->checkDataIsValid($request->request->all(), $this->createNewForm($entity, $id));
        if (!$form['valid']) {
            return new ErrorResponse(
                'validation error(s)',
                InnerErrorCodes::PUT_ENTITY_VALIDATION_FAILED,
                ErrorResponse::HTTP_BAD_REQUEST,
                $form['errors']
            );
        }
        $this->entityManager->flush();

        return new EntityResponse($entity, 'entity updated');
    }

    /**
     * @Rest(requirements= {"id": "\d+"})
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteAction(int $id): JsonResponse
    {
        $entity = $this->findEntityById($id);

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

    protected function findEntityById(int $id): HelpcrunchEntity
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    protected function getNewEntity(): HelpcrunchEntity
    {
        return new static::$entityClassName;
    }

    protected function createNewForm(HelpcrunchEntity $entity, $entityId = false): FormInterface
    {
        return $this->createForm($entity->getFormType(), $entity, ['entity_id' => $entityId]);
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
