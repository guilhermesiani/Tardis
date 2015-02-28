<?php

namespace Pascutti\Tardis\Proxy;

use Doctrine\Common\Cache\Cache;
use Pascutti\Tardis\Identity\IdentityGenerator;

class CacheFetch implements PrefixInterceptor
{
    /**
     * @var Doctrine\Common\Cache\Cache
     */
    private $cacheAdapter = null;
    /**
     * @var Pascutti\Tardis\Identity\IdentityGenerator
     */
    private $identityGenerator = null;

    public function __construct(Cache $cache, IdentityGenerator $idGenerator)
    {
        $this->cacheAdapter = $cache;
        $this->identityGenerator = $idGenerator;
    }

    public function __invoke($proxy, $instance, $methodName, $methodArguments, &$returnEarly)
    {
        $cacheId = $this->identityGenerator->createIdFor($methodName, $methodArguments);
        if (false == $this->cacheAdapter->contains($cacheId)) {
            return;
        }

        $returnEarly = true;

        return $this->cacheAdapter->fetch($cacheId);
    }
}
