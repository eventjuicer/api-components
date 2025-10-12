<?php

namespace Eventjuicer\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
	
	
	protected $table = 'bob_tags';
	
	public $timestamps = false;

	protected $guarded = array('id');
	
	

	function users()
	{
		return $this->belongsToMany('Models\User');
	}

	function posts()
	{
    	return $this->belongsToMany('Models\Post', "editorapp_post_tag", "tag_id", "xref_id")->withPivot('organizer_id');
	}


	function costTags()
	{
		return $this->hasMany('Models\CostTag');
	}

	function costs()
	{
    	return $this->belongsToMany('Models\Cost', "costapp_document_tags", "tag_id", "xref_id")->withPivot('organizer_id', 'group_id', 'event_id', 'originated_at', 'created_at');
	}

	function latestPosts()
	{
    	return $this->belongsToMany('Models\Post', "editorapp_post_tag", "tag_id", "xref_id")->withPivot('organizer_id')->orderBy("publishedon", "DESC");
	}


	function posttags()
	{
    	return $this->hasMany('Models\PostTags');
	}


    public function categories()
    {
        return $this->morphedByMany('Models\Category', 'taggable');
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'bob_taggings', 'tag_id', 'object_id')
                    ->wherePivot("object_name", "ticket")
                    ->withPivot("createdon");
    }

    public function ticketGroups()
    {
        return $this->belongsToMany(TicketGroup::class, 'bob_taggings', 'tag_id', 'object_id')
                    ->wherePivot("object_name", "ticket_group")
                    ->withPivot("createdon");
    }

    public function participantTickets($eventId = null)
    {
        // Get ParticipantTickets for tags on either tickets OR ticket_groups
        $allTicketIds = $this->getTicketIds($eventId);
        
        $query = ParticipantTicket::whereIn('ticket_id', $allTicketIds);
        
        if ($eventId) {
            $query->where('event_id', $eventId);
        }
        
        return $query;
    }

    public function getTicketIds($eventId = null)
    {
        // Returns all ticket IDs for this tag (direct tickets + tickets in tagged groups)
        // Optionally filtered by event_id
        
        // Direct tickets tagged with this tag
        $directTicketIds = $eventId 
            ? $this->tickets()->where('event_id', $eventId)->pluck('id')
            : $this->tickets()->pluck('id');
        
        // Tickets in tagged groups
        $groupsQuery = $eventId 
            ? $this->ticketGroups()->where('event_id', $eventId)
            : $this->ticketGroups();
            
        $groupTicketIds = $groupsQuery->with('tickets')->get()
            ->pluck('tickets')->collapse()->pluck('id');
        
        return $directTicketIds->merge($groupTicketIds)->unique();
    }

    public static function participantIdsByTags($tagIds, $eventId)
    {
        // Get participant IDs that have tickets with ALL specified tags for a given event
        // 1. For each tag, get ticket IDs (from event, includes tickets + ticket groups)
        // 2. Intersect to find tickets that have ALL tags
        // 3. Query ParticipantTicket pivot for those tickets in that event
        
        $ticketsWithAllTags = collect($tagIds)
            ->map(function($id) use ($eventId) {
                return static::find($id)->getTicketIds($eventId);
            })
            ->reduce(function($carry, $ids) {
                return $carry ? $carry->intersect($ids) : $ids;
            });
        
        return ParticipantTicket::where('event_id', $eventId)
            ->whereIn('ticket_id', $ticketsWithAllTags)
            ->where('sold', 1)
            ->pluck('participant_id')
            ->unique();
    }

}
