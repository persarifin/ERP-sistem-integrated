<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    use Macroable;
    
    protected $availableRelations = [];
    protected $defaultRelations = [];
    protected $relationships = [];
    protected $resourceType = '';
    protected $identifiedBy = 'id';
    protected $model;

    public function toArray($request)
    {
        return $this->transformResponse(parent::toArray($request));
    }

    protected function transformResponse($array)
    {
        if ($this->resource !== null) {
            $response = [
                'type' => $this->getResourceType(),
                'id' => (string) $this->getIdentifier(),
                'attributes' => $array,
            ];
            
            $relationships = $this->getLoadedRelationships();
            if (count($relationships) > 0) {
                $response['relationships'] = $relationships;
            }
            
            return $response;
        }

        return [];
    }

    public function getAvailableRelations()
    {
        return $this->availableRelations;
    }
    
    public function getDefaultRelations()
    {
        return $this->defaultRelations;
    }

    public function getIdentifiedBy()
    {
        return $this->identifiedBy;
    }
    
    public function getIdentifier()
    {
        if (!isset($this->{$this->identifiedBy})) {
            return null;
        }

        return $this->{$this->identifiedBy};
    }

    public function getResourceType()
    {
        return $this->resourceType;
    }

    public function getLoadedRelationships()
    {
        $related = [];
        foreach ($this->relationships as $relationship) {
            $relation = $this->getRelationshipFor($relationship);

            if ($relation !== null) {
                $related[$relationship] = $relation;
            }
        }

        return $related;
    }

    protected function getRelationshipFor($relationship)
    {
        $methodName = 'get' . Str::studly($relationship) . 'Relation';
        $relationship = $this->$methodName();

        if ($relationship->resource !== null && $relationship->collects) {
            return $relationship->map(function ($item) {
                return [
                    'type'=> $item->getResourceType(),
                    'id' => (string) $item->getIdentifier()
                ];
            });
        }

        if ($relationship->resource !== null) {
            return [
                'type' => $relationship->getResourceType(),
                'id' => (string) $relationship->getIdentifier(),
            ];
        }

        return null;
    }

    public function addRelationship($relationship)
    {
        $this->relationships[] = $relationship;
    }
}
