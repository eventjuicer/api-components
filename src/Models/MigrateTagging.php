<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class MigrateTagging extends Model
{
    protected $table = "bob_taggings";



    public function tag()
    {
        return $this->hasOne("Models\Tag", "id", "tag_id");
    }

}
