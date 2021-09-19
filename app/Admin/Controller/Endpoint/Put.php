<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Endpoint;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Node\EndpointService;
use KejawenLab\Application\Node\Model\EndpointInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::EDIT})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
*/
final class Put extends AbstractController
{
    public function __construct(private EndpointService $service)
    {
    }

    #[Route(path: '/services/endpoints/{id}/edit', name: Put::class, methods: ['GET'])]
    public function __invoke(Request $request, string $id): Response
    {
        $endpoint = $this->service->get($id);
        if (!$endpoint instanceof EndpointInterface) {
            $this->addFlash('error', 'sas.page.endpoint.not_found');

            return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
        }

        $this->addFlash('id', $endpoint->getId());

        return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
    }
}
