<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class SocialSignInRequest extends Model
{

    protected $table = "eventjuicer_social_requests";

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

   

}

/*

CREATE TABLE `eventjuicer_social_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `uuid` char(40) NOT NULL,
  `organizer_id` int(11) NOT NULL DEFAULT '0',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `redirect_to` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `eventjuicer_social_requests` ADD `service` ENUM('linkedin','twitter','facebook','xing') NOT NULL DEFAULT 'linkedin' AFTER `uuid`;


ALTER TABLE `eventjuicer_social_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `organizer_id` (`organizer_id`),
  ADD KEY `created_at` (`created_at`);

ALTER TABLE `eventjuicer_social_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;



*/
