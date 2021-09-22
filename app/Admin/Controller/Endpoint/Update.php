<?php

declare(strict_types=1);

namespace KejawenLab\Application\Admin\Controller\Endpoint;

use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\Application\Admin\Controller\Node\Main as NodeMain;
use KejawenLab\Application\Domain\Model\NodeInterface;
use KejawenLab\Application\Domain\NodeService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::EDIT})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Update extends AbstractController
{
    public function __construct(private KernelInterface $kernel, private NodeService $nodeService)
    {
    }

    #[Route(path: '/services/nodes/{nodeId}/endpoints/update', name: Update::class, methods: ['GET'], priority: 255)]
    public function __invoke(Request $request, string $nodeId): Response
    {
        /** @var NodeInterface $node */
        $node = $this->nodeService->get($nodeId);
        if (null === $node) {
            $this->addFlash('error', 'sas.page.node.not_found');

            return new RedirectResponse($this->generateUrl(NodeMain::class));
        }

        $console = new Application($this->kernel);
        $console->setAutoExit(false);

        $input = new ArrayInput(['command' => 'syncdation:node:endpoint-update']);
        $output = new NullOutput();
        $console->run($input, $output);

        $this->addFlash('info', 'sas.page.endpoint.updated');

        return new RedirectResponse($this->generateUrl(Main::class, array_merge($request->query->all(), ['nodeId' => $nodeId])));
    }
}
