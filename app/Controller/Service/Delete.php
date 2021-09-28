<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Service;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\ServiceInterface;
use KejawenLab\Application\Domain\ServiceService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="SERVICE", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Delete extends AbstractFOSRestController
{
    public function __construct(private ServiceService $service, private TranslatorInterface $translator)
    {
    }

    /**
     * @Rest\Delete("/services/{id}", name=Delete::class)
     *
     * @OA\Tag(name="Service")
     * @OA\Response(
     *     response=204,
     *     description="Service deleted"
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
        $service = $this->service->get($id);
        if (!$service instanceof ServiceInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.service.not_found', [], 'pages'));
        }

        $this->service->remove($service);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
