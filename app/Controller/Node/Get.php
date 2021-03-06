<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Node;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\NodeService;
use KejawenLab\Application\Entity\Node;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="NODE", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Get extends AbstractFOSRestController
{
    public function __construct(private NodeService $service, private TranslatorInterface $translator)
    {
    }

    /**
     * @Rest\Get("/nodes/{id}", name=Get::class)
     *
     * @OA\Tag(name="Node")
     * @OA\Response(
     *     response=200,
     *     description= "Node detail",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 ref=@Model(type=Node::class, groups={"read"})
     *             )
     *         )
     *     }
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param string $id
     *
     * @return View
     */
    public function __invoke(string $id): View
    {
        $node = $this->service->get($id);
        if (!$node instanceof NodeInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.node.not_found', [], 'pages'));
        }

        return $this->view($node);
    }
}
