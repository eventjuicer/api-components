<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Context extends Model
{
   
	
	protected $table = 'eventjuicer_contexts';
	
	public $timestamps = false;

    protected $fillable = ["slug", "description"];


	public function tickets()
    {
        return $this->morphedByMany('Models\Ticket', 'contextable');
    }

    public function participants()
    {
        return $this->morphedByMany('Models\Participant', 'contextable');
    }

    public function widgets()
    {
        return $this->morphedByMany('Models\Widget', 'contextable');
    }

    /*
    MorphToMany morphedByMany( string $related, string $name, string $table = null, string $foreignKey = null, string $otherKey = null)

    MorphToMany morphToMany( string $related, string $name, string $table = null, string $foreignKey = null, string $otherKey = null, bool $inverse = false)
    
    */

}
