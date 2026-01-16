<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;
 

class Company extends Model
{


    protected $fillable = ['featured','promo','admin_id'];

    protected $table = "eventjuicer_companies";
    
    protected $dates = ['assigned_at', 'stats_updated_at'];


    public function assignedBy()
    {
            return $this->belongsTo(Participant::class, "id", "owner_id");

    }

    public function participants()
    {   
        return $this->hasMany(Participant::class);
    }

    public function visitors()
    {   
        return $this->hasMany(Visitor::class);
    }

    public function people()
    {   
        return $this->hasMany(CompanyPeople::class);
    }

    public function meetups()
    {   
        return $this->hasMany(Meetup::class);
    }

    public function creatives()
    {
        return $this->hasMany(Creative::class);
    }


    public function organizer()
    {
        return $this->belongsTo(Organizer::class, "id", "scanned_id");
    }

    public function data()
    {
        return $this->hasMany(CompanyData::class);
    }

    public function representatives() {
        
        return $this->hasMany(CompanyRepresentative::class);
    }

    public function posts() {
        
        return $this->hasMany(Post::class);
    }
    
    public function group()
    {
        return $this->belongsTo(Group::class, "id", "owner_id");
    }

    public function admin()
    {
        return $this->belongsTo(CompanyAdmin::class, "admin_id");
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function logs(){
        return $this->morphMany(UserLog::class, 'loggable');
    }

    public function images(){
        return $this->morphMany(PostImage::class, 'imageable');
    }




}
