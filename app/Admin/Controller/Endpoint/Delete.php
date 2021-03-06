<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Endpoint;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Domain\EndpointService;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Delete extends AbstractController
{
    public function __construct(private EndpointService $service)
    {
    }

    /**
     * @Route("/endpoints/{id}/delete", name=Delete::class, methods={"GET"})
     */
    public function __invoke(string $id): Response
    {
        $endpoint = $this->service->get($id);
        if (!$endpoint instanceof EndpointInterface) {
            $this->addFlash('error', 'sas.page.endpoint.not_found');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        $this->service->remove($endpoint);

        $this->addFlash('info', 'sas.page.endpoint.deleted');

        return new RedirectResponse($this->generateUrl(Main::class));
    }
}
