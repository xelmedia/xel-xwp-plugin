<?php
declare(strict_types=1);

namespace Xel\XWP\Rest;


class RestRoute {
    protected $methodName;
    protected $classPath;
    protected $pathUri;
    protected $requestType;

    public function __construct($methodName, $class, $pathUri, $requestType) {
        $reflectionClass = new \ReflectionClass($class);

        $this->methodName = $methodName;
        $this->pathUri = $pathUri;
        $this->classPath = $reflectionClass->getName();
        $this->requestType = $requestType;
    }

    public function getMethodName() {
        return $this->methodName;
    }

    public function getClassPath() {
        return $this->classPath;
    }

    public function getPathUri() {
        return $this->pathUri;
    }

    public function getRequestType() {
        return $this->requestType;
    }
}