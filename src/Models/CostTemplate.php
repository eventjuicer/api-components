<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class CostTemplate extends Model
{
    protected $table = "costapp_document_templates";

    protected $casts = array(

    	"data" => "array"
    );

   	protected $fillable = ['name', 'data'];

}
