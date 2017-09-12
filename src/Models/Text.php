<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
   
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'eventjuicer_texts';

  //  protected $primaryKey = 'name';


  //  protected $primaryKey = array('name', 'textable_id', 'textable_type');

    //public $incrementing = false; 


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'data'];


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['user_id','textable_type', 'textable_id', 'created_at', 'updated_at']; //

    //public $timestamps = false;

    //protected $dates = ['updatedon'];


    protected $casts = ["data"=>"array"];


    public function textable()
    {
        return $this->morphTo();
    }
/*
    //http://stackoverflow.com/questions/24152048/laravel-seed-table-with-composite-primary-key

    protected function setKeysForSaveQuery(\Illuminate\Database\Eloquent\Builder $query)
    {
        if(is_array($this->primaryKey))
        {
            foreach($this->primaryKey as $pk)
            {
                $query->where($pk, '=', $this->original[$pk]);
            }
            return $query;
        }
        else
        {
            return parent::setKeysForSaveQuery($query);
        }
    }

*/

}
