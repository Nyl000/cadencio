<?php

namespace Cadencio\Services\Security;

interface ProviderInterface {
    public function test();
    public function getModel();
}