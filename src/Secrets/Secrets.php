<?php

namespace KignOrg\GraphApiAdapter\Secrets;

interface Secrets {
    public function getTenantId();
    public function getClientId();
    public function getClientSecret();
}
