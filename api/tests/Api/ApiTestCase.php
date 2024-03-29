<?php

namespace App\Tests\Api;

use App\Entity\User;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\KernelBrowser;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class ApiTestCase extends KernelTestCase
{
    use Factories;
    use HasBrowser {
        browser as baseKernelBrowser;
    }
    use ResetDatabase;

    protected function browser(array $options = [], array $server = [], false|User|null $user = null): KernelBrowser
    {
        $kernelBrowser = $this->baseKernelBrowser()
            ->setDefaultHttpOptions(
                HttpOptions::create()->withHeader('Accept', 'application/ld+json')
            );

        if (false !== $user) {
            $kernelBrowser->actingAs($user ?? UserFactory::new()->create()->object());
        }

        return $kernelBrowser;
    }
}
