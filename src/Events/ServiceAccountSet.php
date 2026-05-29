<?php

namespace TorMorten\Firestore\Events;

use TorMorten\Firestore\Support\ServiceAccount;

class ServiceAccountSet {
    public function __construct(public ServiceAccount $serviceAccount)
    {
    }
}
