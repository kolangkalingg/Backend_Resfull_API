<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    //mendefinisikan atribut

    public $status;
    public $message;
    public $resource;




    //CONSTRUCTOR
    public function __construct($status, $message, $resource)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }




    public function toArray($request)
    {
        return [
            'success' => $this->status,
            'message' => $this->message,
            'data' => $this->resource,
        ];
    }
}
