<?php

/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Phly\Expressive\OAuth2ClientAuthentication;

use League\OAuth2\Client\Provider;
use Psr\Container\ContainerInterface;

class OAuth2ProviderFactory
{
    public const PROVIDER_MAP = [
        'debug'  => Debug\DebugProvider::class,
        'facebook' => Provider\Facebook::class,
        'github' => Provider\Github::class,
        'google' => Provider\Google::class,
        'instagram' => Provider\Instagram::class,
        'linkedin' => Provider\LinkedIn::class,
    ];

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @throws Exception\UnsupportedProviderException
     * @throws Exception\MissingProviderConfigException
     */
    public function createProvider(string $name) : Provider\AbstractProvider
    {
        $knownProviders = array_keys(self::PROVIDER_MAP);
        if (! in_array($name, $knownProviders, true)) {
            throw Exception\UnsupportedProviderException::forProvider($name, $knownProviders);
        }

        $config = $this->container->get('config')['oauth2clientauthentication'] ?? [];

        if (! isset($config[$name])) {
            throw Exception\MissingProviderConfigException::forProvider($name);
        }

        $provider = self::PROVIDER_MAP[$name];

        return new $provider($config[$name]);
    }
}
