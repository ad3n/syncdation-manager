<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\ApiClient\Query;

use Doctrine\ORM\QueryBuilder;
use KejawenLab\ApiSkeleton\ApiClient\Model\ApiClientInterface;
use KejawenLab\ApiSkeleton\ApiClient\Model\ApiClientRequestInterface;
use KejawenLab\ApiSkeleton\Pagination\Query\AbstractQueryExtension;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class FilterRequestByApiClientExtension extends AbstractQueryExtension
{
    public function apply(QueryBuilder $queryBuilder, Request $request): void
    {
        $filter = $request->attributes->get('id');
        if (!$filter) {
            return;
        }

        $queryBuilder->join(sprintf('%s.node', $this->aliasHelper->findAlias('root')), $this->aliasHelper->findAlias('apiClient'));
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq(
                sprintf('%s.id', $this->aliasHelper->findAlias('apiClient')),
                $queryBuilder->expr()->literal($filter)
            )
        );
    }

    public function support(string $class, Request $request): bool
    {
        return in_array(ApiClientRequestInterface::class, class_implements($class));
    }
}
