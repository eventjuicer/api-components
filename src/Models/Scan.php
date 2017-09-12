<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

use Eventjuicer\Models\ScanComment;
use Eventjuicer\Models\Participant;


class Scan extends Model
{

     

    protected $table = "eventjuicer_barcode_scans";
    
    protected $guarded = ['id'];

    
    public function comments()
    {
        return $this->hasMany(ScanComment::class, "scan_id")->orderBy("created_at", "DESC");;
    }


    public function owner()
    {   
         return $this->belongsTo(Participant::class, "id", "owner_id");
    }


    public function participant()
    {
         return $this->hasOne(Participant::class, "id", "scanned_id");
    }






}
