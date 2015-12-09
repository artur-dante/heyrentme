<?php

namespace AppBundle\Entity;

/**
 * FeatureSectionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FeatureSectionRepository extends \Doctrine\ORM\EntityRepository
{
    public function getBySubcategorySorted($subcategoryId) {
        $sql = 'select fc from AppBundle:FeatureSection fc where fc.subcategory = :subcategory order by fc.position';
        $q = $this->getEntityManager()->createQuery($sql);
        $q->setParameter(':subcategory', $subcategoryId);
        return $q->getResult();
    }
    
    public function getFeaturesSorted($featureSectionId, $freetext) {
        $sql = 'select f '.
                'from AppBundle:Feature f '.
                'where f.featureSection = :featureSection '.
                '   and f.freetext = :freetext '.
                'order by f.position';
        $q = $this->getEntityManager()->createQuery($sql);
        $q->setParameter(':featureSection', $featureSectionId);
        $q->setParameter(':freetext', $freetext);
        return $q->getResult();
    }
    public function getGridOverview($sortColumn, $sortDirection, $pageSize, $page) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        // build query
        $qb->select(array('fs', 's'))
            ->from('AppBundle:FeatureSection', 'fs')
            ->join('fs.subcategory', 's');
        // sort by
        if (!empty($sortColumn)) {
            if (!empty($sortDirection)) {
                $qb->orderBy($sortColumn, $sortDirection);
            }
            else {
                $qb->orderBy($sortColumn);
            }
        }

        $q = $qb->getQuery();
        // page and page size
        if (!empty($pageSize)) {
            $q->setMaxResults($pageSize);
        }
        if (!empty($page) && $page != 1) {
            $q->setFirstResult(($page - 1) * $pageSize);
        }
        return $q->getResult();        
    }
    public function countAll() {
        return $this->createQueryBuilder('fs')
            ->select('count(fs.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
