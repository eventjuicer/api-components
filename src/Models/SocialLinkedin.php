<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLinkedin extends Model
{

    protected $table = "eventjuicer_social_linkedin";
    protected $fillable = [
      "linkedin_id", 
      "locale",
      "fname", 
      "lname", 
      "email", 
      "avatar"
    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // public function votes()
    // {
    //     return $this->hasMany(SocialVote:: class);
    // }

    public function votes()
    {
        return $this->morphMany(SocialVote::class, 'voteable');
    }


}

/*


CREATE TABLE `eventjuicer_social_linkedin` (
  `id` int(10) UNSIGNED NOT NULL,
  `linkedin_id` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `fname` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `lname` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `avatar` text CHARACTER SET utf8 COLLATE utf8_polish_ci,
  `organizer_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `linkedin_client_id` varchar(128) COLLATE utf8_polish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


ALTER TABLE `eventjuicer_social_linkedin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `check` (`linkedin_id`,`linkedin_client_id`),
  ADD KEY `linkedin_id` (`linkedin_id`),
  ADD KEY `email` (`email`),
  ADD KEY `organizer_id` (`organizer_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `created_at` (`created_at`);


ALTER TABLE `eventjuicer_social_linkedin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


*/
