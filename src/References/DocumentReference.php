<?php

namespace TorMorten\Firestore\References;

use Psr\Http\Message\UriInterface;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Database\Reference\Validator;
use Kreait\Firebase\Exception\InvalidArgumentException;
use TorMorten\Firestore\Document;
use TorMorten\Firestore\Http\ApiClient;
use TorMorten\Firestore\Support\ReverseMapValues;

/**
 * A Reference represents a specific location in your database and can be used
 * for reading or writing data to that database location.
 *
 * @see https://firebase.google.com/docs/reference/js/firebase.database.Reference
 */
class DocumentReference extends Query
{
    /**
     * @var Document
     */
    protected $document;

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @var ApiClient
     */
    protected $apiClient;
    /**
     * @var null
     */
    protected $path;

    /**
     * Creates a new Reference instance for the given URI which is accessed by
     * the given API client and validated by the Validator (obviously).
     *
     * @param UriInterface $uri
     * @param ApiClient $apiClient
     * @param Validator|null $validator
     *
     * @throws InvalidArgumentException if the reference URI is invalid
     */
    public function __construct(UriInterface $uri, ApiClient $apiClient = null, $path = null)
    {
        $this->uri = $uri;
        $this->apiClient = $apiClient;
        $this->path = $path;
    }

    public function setDocument(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Write data to this database location.
     *
     * This will overwrite any data at this location and all child locations.
     *
     * Passing null for the new value is equivalent to calling {@see remove()}:
     * all data at this location or any child location will be deleted.
     *
     * @param mixed $value
     *
     * @return DocumentReference
     * @throws \TorMorten\Firestore\Exceptions\ApiException if the API reported an error
     *
     */
    public function set($value, $merge = false): self
    {

        $payload = [
            'name' => basename($this->uri->getPath()),
            'fields' => $value,
        ];

        if ($merge) {
            $paths = $this->valueMapper->encodeFieldPaths($value);
            $prefix = '&updateMask.fieldPaths=';
            $query = $prefix . implode($prefix, $paths);
            $uri = $this->uri->withQuery("updateMask.fieldPaths=message$query");
        } else {
            $uri = $this->uri;
        }

        $this->apiClient->patch($uri, $payload);

        return $this;
    }

    /**
     * Write data to this database location.
     *
     * This will overwrite any data at this location and all child locations.
     *
     * Passing null for the new value is equivalent to calling {@see remove()}:
     * all data at this location or any child location will be deleted.
     *
     * @param mixed $value
     *
     * @return DocumentReference
     * @throws \TorMorten\Firestore\Exceptions\ApiException if the API reported an error
     *
     */
    public function store($value): self
    {

        $payload = [
            'fields' => resolve(ReverseMapValues::class)->map($value),
        ];

        $this->apiClient->post($this->uri, $payload);

        return $this;
    }

    /**
     * Write data to this database location.
     *
     * This will overwrite any data at this location and all child locations.
     *
     * Passing null for the new value is equivalent to calling {@see remove()}:
     * all data at this location or any child location will be deleted.
     *
     * @param mixed $value
     *
     * @return DocumentReference
     * @throws \TorMorten\Firestore\Exceptions\ApiException if the API reported an error
     *
     */
    public function update($value): self
    {

        $payload = [
            'name' => $this->path,
            'fields' => resolve(ReverseMapValues::class)->map($value),
        ];

        $this->apiClient->patch($this->uri, $payload);

        return $this;
    }

    /**
     * Returns a data snapshot of the current location.
     *
     * @return Document
     * @throws \TorMorten\Firestore\Exceptions\ApiException if the API reported an error
     *
     */
    public function snapshot()
    {
        $value = $this->apiClient->get($this->uri);

        if (!$this->document) {
            $this->document = new Document($value['fields'], $this);
        } else {
            $this->document->update(
                $value['fields']
            );
        }

        return $this->document;
    }

    public function delete()
    {
        return $this->apiClient->delete($this->uri);
    }
}
