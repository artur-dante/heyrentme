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
}
