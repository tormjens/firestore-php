<?php

namespace TorMorten\Firestore\References;

use Google\Service\Firestore\ListDocumentsResponse;
use TorMorten\Firestore\Requests\Collection as CollectionRequest;

class Collection extends \Illuminate\Support\Collection
{
    protected $collection;

    public function __construct($documents, $collection = null)
    {
        if($documents instanceof ListDocumentsResponse) {
            $this->mapToDocuments($documents);
        } else {
            $this->items = $documents;
        }
        $this->collection = $collection;
    }

    protected function mapToDocuments($documents)
    {
        foreach($documents->getDocuments() as $document) {
            $this->items[] = new Document($document->getFields()['id']->stringValue, $document, $this->collection);
        }
    }
}
