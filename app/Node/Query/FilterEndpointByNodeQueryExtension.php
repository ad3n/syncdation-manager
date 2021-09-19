<?php

declare(strict_types=1);

namespace KejawenLab\Application\Node\Query;

use Doctrine\ORM\QueryBuilder;
use KejawenLab\ApiSkeleton\Pagination\Query\AbstractQueryExtension;
use KejawenLab\ApiSkeleton\Util\StringUtil;
use KejawenLab\Application\Node\Model\EndpointInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@gmail.com>
 */
final class FilterEndpointByNodeQueryExtension extends AbstractQueryExtension
{
    public function apply(QueryBuilder $queryBuilder, Request $request): void
    {
        $filter = $request->attributes->get('nodeId');
        if (!$filter) {
            return;
        }

        $queryBuilder->join(sprintf('%s.node', $this->aliasHelper->findAlias('root')), $this->aliasHelper->findAlias('node'));
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq(
                sprintf('%s.id', $this->aliasHelper->findAlias('node')),
                $queryBuilder->expr()->literal($filter)
            )
        );
    }

    public function support(string $class, Request $request): bool
    {
        return in_array(EndpointInterface::class, class_implements($class));
    }
}
