<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Endpoint;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\Application\Node\Model\EndpointInterface;
use KejawenLab\Application\Node\EndpointService;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Delete extends AbstractFOSRestController
{
    public function __construct(private EndpointService $service, private TranslatorInterface $translator)
    {
    }

    /**
     * @Rest\Delete("/endpoints/{id}", name=Delete::class)
     *
     * @OA\Tag(name="Endpoint")
     * @OA\Response(
     *     response=204,
     *     description="Endpoint deleted"
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
        $endpoint = $this->service->get($id);
        if (!$endpoint instanceof EndpointInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.endpoint.not_found', [], 'pages'));
        }

        $this->service->remove($endpoint);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
