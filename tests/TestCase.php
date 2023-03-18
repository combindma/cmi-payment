<?php

namespace Combindma\Cmi\Tests;

use Combindma\Cmi\Cmi;
use Combindma\Cmi\CmiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public Cmi $cmi;

    protected function getPackageProviders($app): array
    {
        return [
            CmiServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('cmi-payment.clientId', 'valid_string');
        $app['config']->set('cmi-payment.storeKey', 'valid_string');
        $app['config']->set('cmi-payment.baseUri', 'https://test.cmi.ma');
        $app['config']->set('cmi-payment.okUrl', 'https://test.cmi.ma');
        $app['config']->set('cmi-payment.failUrl', 'https://test.cmi.ma');
        $app['config']->set('cmi-payment.callbackUrl', 'https://test.cmi.ma');
        $app['config']->set('cmi-payment.shopUrl', 'https://test.cmi.ma');
        $this->cmi = new Cmi();
    }
}
