<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Node;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Form\FormFactory;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\NodeService;
use KejawenLab\Application\Entity\Node;
use KejawenLab\Application\Form\NodeType;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Permission(menu="NODE", actions={Permission::ADD})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Post extends AbstractFOSRestController
{
    public function __construct(private FormFactory $formFactory, private NodeService $service)
    {
    }

    /**
     * @Rest\Post("/nodes", name=Post::class)
     *
     * @OA\Tag(name="Node")
     * @OA\RequestBody(
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 ref=@Model(type=NodeType::class)
     *             )
     *         )
     *     }
     * )
     * @OA\Response(
     *     response=201,
     *     description="Crate new node",
     *     @OA\Schema(
     *         type="object",
     *         ref=@Model(type=Node::class, groups={"read"})
     *     )
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     *
     * @return View
     */
    public function __invoke(Request $request): View
    {
        $form = $this->formFactory->submitRequest(NodeType::class, $request);
        if (!$form->isValid()) {
            return $this->view((array) $form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        /** @var NodeInterface $node */
        $node = $form->getData();
        $this->service->save($node);

        return $this->view($this->service->get($node->getId()), Response::HTTP_CREATED);
    }
}
