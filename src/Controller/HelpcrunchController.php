<?php

namespace Helpcrunch\Controller;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Service\RedisService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Helpcrunch\Traits\FormTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;

abstract class HelpcrunchController extends FOSRestController implements ClassResourceInterface
{
    use FormTrait;

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
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RedisService
     */
    protected $redis;

    public function __construct(EntityManagerInterface $entityManager, RedisService $redis)
    {
        $this->entityManager = $entityManager;
        $this->redis = $redis;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function cgetAction(Request $request): array
    {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', self::DEFAULT_PAGINATION_LIMIT);

        return $this->getRepository()->findEntities($offset, $limit);
    }

    /**
     * @param int $id
     * @return null|object
     */
    public function getAction(int $id)
    {
        return $this->findEntityById($id);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function postAction(Request $request): JsonResponse
    {
        $entity = new static::$entityClassName;

        $form = $this->checkDataIsValid($request->request->all(), $this->createNewForm($entity));
        if (!$form['valid']) {
            return new JsonResponse($form['errors'], Response::HTTP_BAD_REQUEST);
        }

        $entity = $form['entity'];
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        // Get entity from DB with all fields
        $entity = $this->findEntityById($entity->id);

        $serializer = SerializerBuilder::create()->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
        $entity = $serializer->build()->toArray($entity);

        return new JsonResponse($entity, JsonResponse::HTTP_CREATED);
    }

    public function putAction(Request $request, int $id): JsonResponse
    {
        $entity = $this->findEntityById($id);

        $form = $this->checkDataIsValid($request->request->all(), $this->createNewForm($entity), false);
        if (!$form['valid']) {
            return new JsonResponse($form['errors'], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function deleteAction(int $id): JsonResponse
    {
        $entity = $this->findEntityById($id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository(static::$entityClassName);
    }

    protected function findEntityById(int $id): object
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

    protected function createNewForm($entity)
    {
        return $this->createForm($entity->getFormType(), $entity);
    }
}
