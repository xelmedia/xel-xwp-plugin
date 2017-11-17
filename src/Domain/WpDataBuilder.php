<?php
declare(strict_types=1);

namespace Xel\XWP\Domain;


class WpDataBuilder {
    private $label;
    private $name;
    private $enabled;

    public function getLabel() {
        return $this->label;
    }

    public function getName(): String {
        return $this->name;
    }

    public function getEnabled(): bool {
        return $this->enabled;
    }

    public function label(String $label): WpDataBuilder {
        $this->label = $label;
        return $this;
    }

    public function name(String $name): WpDataBuilder {
        $this->name = $name;
        return $this;
    }

    public function enabled(bool $enabled): WpDataBuilder {
        $this->enabled = $enabled;
        return $this;
    }

    public function build(): WpData {
        return new WpData($this);
    }

    public function getEntityClass(): string {
        return WpData::class;
    }
}