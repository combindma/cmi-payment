<?php

it('can test if config file are set', function () {
    expect($this->cmi->getBaseUri())->toBe('https://test.cmi.ma')
        ->and($this->cmi->getShopUrl())->toBe('https://test.cmi.ma');
});
