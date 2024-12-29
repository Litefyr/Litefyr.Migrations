<?php
namespace Litefyr\Migrations\Migrations;

use Neos\ContentRepository\Domain\Model\NodeData;
use Neos\ContentRepository\Migration\Transformations\AbstractTransformation;

class BorderRadiusMigration extends AbstractTransformation
{
    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @var int
     */
    protected $fullRounded;

    /**
     * @var bool
     */
    protected $convertPxToRem = false;

    public function setProperty($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    public function setFullRounded($value)
    {
        $this->fullRounded = $value;
    }

    public function setConvertPxToRem($value)
    {
        $this->convertPxToRem = !!$value;
    }

    /**
     * If the given node has the property this transformation should work on, this
     * returns true.
     *
     * @param NodeData $node
     * @return boolean
     */
    public function isTransformable(NodeData $node)
    {
        return $node->hasProperty($this->propertyName);
    }

    /**
     * Change the property on the given node.
     *
     * @param NodeData $node
     * @return void
     * @throws \Neos\ContentRepository\Exception\NodeException
     */
    public function execute(NodeData $node)
    {
        $currentPropertyValue = $node->getProperty($this->propertyName);

        if (
            $currentPropertyValue === 0 ||
            (is_string($currentPropertyValue) && str_starts_with($currentPropertyValue, '0'))
        ) {
            $node->setProperty($this->propertyName, '0');
            sleep(1);
            return;
        }

        // If it is not numeric, we don't need to do anything
        if (!is_numeric($currentPropertyValue)) {
            return;
        }

        if ($this->fullRounded && (string) $currentPropertyValue === (string) $this->fullRounded) {
            $node->setProperty($this->propertyName, '9999px');
            sleep(1);
            return;
        }

        if (!$currentPropertyValue) {
            return;
        }

        if ($this->convertPxToRem) {
            $currentPropertyValue = sprintf('%srem', (int) $currentPropertyValue / 16);
        } else {
            $currentPropertyValue = sprintf('%spx', $currentPropertyValue);
        }

        $node->setProperty($this->propertyName, $currentPropertyValue);
        sleep(1);
    }
}
