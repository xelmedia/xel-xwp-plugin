<?php
declare(strict_types=1);

namespace Xel\XWP\Domain;

use Xel\Common\Serializable;

class WpData extends Serializable {

    /** @optional */
    protected $label;
    /** @var  String*/
    protected $name;

    public function __construct(WpDataBuilder $builder) {
        $this->name = $builder->getName();
        $this->label = $builder->getLabel();
    }

    public function getLabel(): ?string {
        return $this->label;
    }

    public function getName(): string {
        return $this->name;
    }

    public static function builder(): WpDataBuilder {
        return new WpDataBuilder();
    }
}