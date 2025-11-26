<?php

namespace TorMorten\Firestore\Support;

class ServiceAccount
{
    protected static $serviceAccount;

    public function set($serviceAccount)
    {
        static::$serviceAccount = $serviceAccount;
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
