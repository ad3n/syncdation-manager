<?php

declare(strict_types=1);

namespace KejawenLab\Application\Controller\Endpoint;

use FOS\RestBundle\Controller\Annotations as Rest;
use KejawenLab\ApiSkeleton\ApiClient\Model\ApiClientInterface;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\ApiSkeleton\Security\Service\UserProviderFactory;
use KejawenLab\ApiSkeleton\Security\User;
use KejawenLab\Application\Domain\EndpointService;
use KejawenLab\Application\Domain\Model\EndpointInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Swoole\Coroutine\Channel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Swoole\Coroutine\run;

/**
 * @Permission(menu="ENDPOINT", actions={Permission::VIEW})
 *
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class Call extends AbstractController
{
    public function __construct(
        private EndpointService $service,
        private UserProviderFactory $userProviderFactory,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @Rest\Get("/endpoints/call/{path}", name=Call::class, requirements={"path"=".+"})
     *
     * @OA\Tag(name="Endpoint")
     * @OA\Response(
     *     response=200,
     *     description= "Endpoint detail",
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object"
     *             )
     *         )
     *     }
     * )
     *
     * @Security(name="Bearer")
     *
     * @param Request $request
     * @param string $path
     *
     * @return Response
     */
    public function __invoke(Request $request, string $path): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.user.not_found', [], 'pages'));
        }

        $user = $this->userProviderFactory->getRealUser($user);
        if (!$user instanceof ApiClientInterface) {
            throw new NotFoundHttpException($this->translator->trans('sas.page.user.not_found', [], 'pages'));
        }

        $endpoints = $this->service->getByPath(sprintf('/%s', $path));

        if (1 === count($endpoints)) {
            if (!$this->service->isAllowToRequest($user, $endpoints[0])) {
                return new JsonResponse([
                    'message' => $this->translator->trans('sas.page.endpoint.request_limit', [], 'pages'),
                ], Response::HTTP_TOO_MANY_REQUESTS);
            }
        }

        $limit = 0;
        $result = [];
        $chan = new Channel(1);
        go(function () use ($chan, $endpoints, &$limit, $user, $request) {
            foreach ($endpoints as $endpoint) {
                if (!$this->service->isAllowToRequest($user, $endpoint)) {
                    $limit++;

                    continue;
                }

                if (!$endpoint instanceof EndpointInterface) {
                    continue;
                }

                $chan->push($this->service->call($endpoint, $user, $request->query->all()));
            }
        });
        $chan->close();

        go(function () use ($chan, &$result) {
            $data = $chan->pop();
            if (!empty($data)) {
                $result = $data;
            }
        });

        if ($limit === count($endpoints)) {
            return new JsonResponse([
                'message' => $this->translator->trans('sas.page.endpoint.request_limit', [], 'pages'),
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return new JsonResponse($result);
    }
}
