<?php

namespace TorMorten\Firestore\Support;

use TorMorten\Firestore\Events\ServiceAccountSet;

class ServiceAccount
{
    protected static $serviceAccount;

    public function set($serviceAccount)
    {
        static::$serviceAccount = $serviceAccount;

        event(new ServiceAccountSet($this));
    }

    public function get()
    {
        if (!static::$serviceAccount) {
            if (config('firestore.service_account_file', false) && file_exists($filePath = base_path(config('firestore.service_account_file')))) {
                $this->set(json_decode(file_get_contents($filePath), true));
            }
        }

        return static::$serviceAccount;
    }

    public function getParentId()
    {
        return join('/', [
            'projects',
            $this->get()['project_id'],
            'databases',
            '(default)',
            'documents'
        ]);
    }
}
