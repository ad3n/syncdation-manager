<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Admin\Controller;

use KejawenLab\ApiSkeleton\Admin\AdminContext;
use KejawenLab\Application\Domain\EndpointService;
use KejawenLab\Application\Domain\NodeService;
use KejawenLab\Application\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Base;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class DashboardController extends Base
{
    public function __construct(
        private NodeService       $nodeService,
        private ServiceRepository $serviceRepository,
        private EndpointService   $endpointService,
    ) {
    }

    #[Route(path: '/', name: AdminContext::ADMIN_ROUTE, methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        return $this->render('dashboard/layout.html.twig', [
            'page_title' => 'sas.page.dashboard',
            'node' => $this->nodeService->total(),
            'service' => count($this->serviceRepository->findAll()),
            'endpoint' => $this->endpointService->total(),
            'uptime' => $this->nodeService->calculateUptime(),
        ]);
    }
}
