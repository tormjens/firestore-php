<?php

namespace TorMorten\Firestore\Http;

use Google\Client;
use Google\Service\Firestore;
use Illuminate\Support\Str;
use TorMorten\Firestore\Requests\Collection;
use TorMorten\Firestore\Support\ServiceAccount;

class FirestoreApi
{
    protected Firestore $client;
    protected ServiceAccount $serviceAccount;

    public function __construct()
    {
        $this->serviceAccount = resolve(ServiceAccount::class);
        $this->buildGoogleClient();
    }

    protected function buildGoogleClient()
    {
        $client = new Client();
        if ($serviceAccount = $this->serviceAccount->get()) {
            $client->setAuthConfig($serviceAccount);
        }
        $client->setApplicationName("TorMorten\\Firestore");
        $client->setScopes(['https://www.googleapis.com/auth/datastore']);

        $this->client = new Firestore($client);
    }

    public function resource()
    {
        return $this->client->projects_databases_documents;
    }

    public function collection($path)
    {
        return new Collection($this->client->projects_databases_documents, $path);
    }

}
