<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Node;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Form\FormFactory;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Entity\Node;
use KejawenLab\Application\Form\NodeType;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\NodeService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="NODE", actions={Permission::EDIT})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Put extends AbstractFOSRestController
{
    public function __construct(
        private FormFactory $formFactory,
        private NodeService $service,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @Rest\Put("/services/nodes/{id}", name=Put::class)
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
     *     response=200,
     *     description="Update node",
     *     @OA\Schema(
     *         type="object",
     *         ref=@Model(type=Node::class, groups={"read"})
     *     )
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param string $id
     *
     * @return View
     */
    public function __invoke(Request $request, string $id): View
    {
        $node = $this->service->get($id);
        if (!$node instanceof NodeInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.node.not_found', [], 'pages'));
        }

        $form = $this->formFactory->submitRequest(NodeType::class, $request, $node);
        if (!$form->isValid()) {
            return $this->view((array) $form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $this->service->save($node);

        return $this->view($this->service->get($node->getId()));
    }
}
