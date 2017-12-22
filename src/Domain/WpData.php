<?php
declare(strict_types=1);

namespace Xel\XWP\Domain;


class WpData implements \JsonSerializable {
    protected $label;
    protected $name;
    protected $enabled;
    protected $versionNumber;
    protected $websiteUrl;

    public function __construct(WpDataBuilder $builder) {
        $this->name = $builder->getName();
        $this->label = $builder->getLabel();
        $this->enabled = $builder->getEnabled();
        $this->versionNumber = $builder->getVersionNumber();
        $this->websiteUrl = $builder->getWebsiteUrl();
    }

    public function getLabel() {
        return $this->label;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEnabled(): bool {
        return $this->enabled ?? false;
    }

    public function getVersionNumber() {
        return $this->versionNumber;
    }

    public function getWebsiteUrl() {
        return $this->websiteUrl;
    }

    public static function builder(): WpDataBuilder {
        return new WpDataBuilder();
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize() {
        $return = [];

        // Set fields
        $return["name"] = $this->name;
        $return["enabled"] = $this->enabled === null ? false : $this->enabled;

        // Optionals
        if($this->label !== null) $return["label"] = $this->label;
        if($this->versionNumber !== null) $return["versionNumber"] = $this->versionNumber;
        if($this->websiteUrl !== null) $return["websiteUrl"] = $this->websiteUrl;
        return $return;
    }
}