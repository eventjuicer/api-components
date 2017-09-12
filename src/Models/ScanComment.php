<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Models\Scan;

class ScanComment extends Model
{
     
    protected $table = "eventjuicer_barcode_scan_comments";
    
    protected $guarded = ['id'];

    
    public function scan()
    {
    	return $this->belongsTo(Scan::class);
    }



}
