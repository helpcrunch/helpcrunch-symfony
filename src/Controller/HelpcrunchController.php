<?php

namespace Helpcrunch\Controller;

use App\Entity\BaseEntity;
use App\Service\RedisService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @var string
     */
    public static $entityClassName;

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
     * @return JsonResponse
     * @throws \Exception
     */
    public function postAction(Request $request): JsonResponse
    {
        $entity = $this->createEntity($request);

        $token = $entity->generateToken();

        $this->redis->pushData($token, $entity->__get('id'));

        return new JsonResponse($token, JsonResponse::HTTP_CREATED);
    }

    public function putAction(Request $request, int $id): JsonResponse
    {
        $entity = $this->getRepository()->find($id);

        $form = $this->checkDataIsValid($request, $entity);
        if (!$form['valid']) {
            return new JsonResponse($form['errors'], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function deleteAction(string $token): JsonResponse
    {
        $id = $this->redis->getData($token);
        if (!$id) {
            return new JsonResponse('Token is invalid', Response::HTTP_BAD_REQUEST);
        }

        $entity = $this->getRepository()->find($id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->redis->delete($token);
        if (!empty($sessionData = $this->redis->getArrayData($this->redis::SESSION_DATA_KEY . $token))) {
            foreach ($sessionData as $field => $value) {
                $this->redis->deleteArrayData($this->redis::SESSION_DATA_KEY . $token, $field);
            }
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository(static::$entityClassName);
    }

    protected function getErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form as $child) {
            foreach ($child->getErrors(true) as $error) {
                $errors[$child->getName()] = $error->getMessage();
            }
        }

        return $errors;
    }

    protected function checkDataIsValid(Request $request, $entity): array
    {
        $form = $this->createForm($entity->getFormType(), $entity);
        $form->submit($request->request->all());
        if (!$form->isValid()) {
            return [
                'valid' => false,
                'errors' => $this->getErrors($form),
            ];
        }

        return [
            'valid' => true,
            'entity' => $form->getData(),
        ];
    }

    protected function createEntity(Request $request): object
    {
        if (!($entity = $this->getRepository()->find($request->request->get('id')))) {
            $entity = $this->getNewEntity();

            $form = $this->checkDataIsValid($request, $entity);
            if (!$form['valid']) {
                return new JsonResponse($form['errors'], Response::HTTP_BAD_REQUEST);
            }

            $entity = $form['entity'];
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        return $entity;
    }

    protected function getNewEntity(): BaseEntity
    {
        return new static::$entityClassName;
    }
}
