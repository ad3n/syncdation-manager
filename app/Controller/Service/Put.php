<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Service;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Form\FormFactory;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\Model\ServiceInterface;
use KejawenLab\Application\Domain\ServiceService;
use KejawenLab\Application\Entity\Service;
use KejawenLab\Application\Form\ElasticsearchServiceType;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Permission(menu="SERVICE", actions={Permission::EDIT})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Put extends AbstractFOSRestController
{
    public function __construct(
        private FormFactory $formFactory,
        private ServiceService $service,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @Rest\Put("/services/{id}", name=Put::class)
     *
     * @OA\Tag(name="Service")
     * @OA\RequestBody(
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 ref=@Model(type=ServiceType::class)
     *             )
     *         )
     *     }
     * )
     * @OA\Response(
     *     response=200,
     *     description="Update service",
     *     @OA\Schema(
     *         type="object",
     *         ref=@Model(type=Service::class, groups={"read"})
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
        $service = $this->service->get($id);
        if (!$service instanceof ServiceInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.service.not_found', [], 'pages'));
        }

        $form = $this->formFactory->submitRequest(ElasticsearchServiceType::class, $request, $service);
        if (!$form->isValid()) {
            return $this->view((array) $form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $this->service->save($service);

        return $this->view($this->service->get($service->getId()));
    }
}
