<?php
namespace Concrete\Core\Entity\Attribute\Key\Type;

use Concrete\Core\Entity\Attribute\Value\Value\SelectValue;
use Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="SelectAttributeKeyTypes")
 */
class SelectType extends Type
{

    public function __construct()
    {
        $this->list = new SelectValueOptionList();
    }

    /**
     * @ORM\OneToOne(targetEntity="Concrete\Core\Entity\Attribute\Value\Value\SelectValueOptionList", cascade={"all"})
     * @ORM\JoinColumn(name="avSelectOptionListID", referencedColumnName="avSelectOptionListID")
     */
    protected $list;

    /**
     * @return mixed
     */
    public function getOptionList()
    {
        return $this->list;
    }

    /**
     * @param mixed $list
     */
    public function setOptionList($list)
    {
        $this->list = $list;
    }

    public function getAttributeValue()
    {
        return new SelectValue();
    }

    /**
     * @ORM\Column(type="boolean")
     */
    protected $akSelectAllowMultipleValues = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $akSelectAllowOtherValues = false;

    /**
     * @ORM\Column(type="string")
     */
    protected $akSelectOptionDisplayOrder = 'display_asc';

    /**
     * @return mixed
     */
    public function getAllowMultipleValues()
    {
        return $this->akSelectAllowMultipleValues;
    }

    /**
     * @param mixed $allowMultipleValues
     */
    public function setAllowMultipleValues($allowMultipleValues)
    {
        $this->akSelectAllowMultipleValues = $allowMultipleValues;
    }

    /**
     * @return mixed
     */
    public function getAllowOtherValues()
    {
        return $this->akSelectAllowOtherValues;
    }

    /**
     * @param mixed $allowOtherValues
     */
    public function setAllowOtherValues($allowOtherValues)
    {
        $this->akSelectAllowOtherValues = $allowOtherValues;
    }

    /**
     * @return mixed
     */
    public function getDisplayOrder()
    {
        return $this->akSelectOptionDisplayOrder;
    }

    /**
     * @param mixed $displayOrder
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->akSelectOptionDisplayOrder = $displayOrder;
    }

    public function mergeAndPersist(EntityManagerInterface $entityManager)
    {
        $tempOptions = array();

        foreach($this->list->getOptions() as $option) {
            if (!$option->getSelectAttributeOptionID()) {
                $tempOptions[] = $option;
                $this->list->getOptions()->removeElement($option);
            }
        }

        $key_type = $entityManager->merge($this);

        foreach($tempOptions as $option) {
            $option->setOptionList($key_type->list);
            $key_type->list->getOptions()->add($option);
        }

        $entityManager->persist($key_type);
        return $key_type;
    }

}
