<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Endpoint;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Node\EndpointService;
use KejawenLab\Application\Node\Model\NodeInterface;
use KejawenLab\Application\Node\NodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Download extends AbstractController
{
    public function __construct(private EndpointService $service, private NodeService $nodeService, private SerializerInterface $serializer)
    {
    }

    #[Route(path: '/services/nodes/{nodeId}/endpoints/download', name: Download::class, methods: ['GET'])]
    public function __invoke(Request $request, string $nodeId): Response
    {
        /** @var NodeInterface $node */
        $node = $this->nodeService->get($nodeId);
        if (null === $node) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(Main::class, $request->query->all()));
        }

        $records = $this->service->total();
        if (10000 < $records) {
            $this->addFlash('error', 'sas.page.error.too_many_records');

            return new RedirectResponse($this->generateUrl(Main::class));
        }

        $response = new Response();
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'text/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s_%s.csv"', 'endpoints', date('YmdHis')));

        $response->setContent($this->serializer->serialize($this->service->all(), 'csv', ['groups' => 'read']));

        return $response;
    }
}
