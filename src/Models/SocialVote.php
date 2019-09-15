<?php

namespace Eventjuicer\Models;

use Illuminate\Database\Eloquent\Model;

class SocialVote extends Model
{

    protected $table = "eventjuicer_social_votes";

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function voteable()
    {
        return $this->morphTo();
    }

}

/*

-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 15 Wrz 2019, 19:19
-- Wersja serwera: 8.0.12
-- Wersja PHP: 7.2.9-1+0~20180910100512.5+stretch~1.gbpdaac35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Baza danych: `30dni`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `eventjuicer_social_votes`
--

CREATE TABLE `eventjuicer_social_votes` (
  `id` int(10) UNSIGNED NOT NULL,
  `participant_id` int(11) NOT NULL DEFAULT '0',
  `widget_id` int(11) NOT NULL DEFAULT '0',
  `voteable_type` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `voteable_id` int(11) NOT NULL DEFAULT '0',
  `organizer_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `eventjuicer_social_votes`
--
ALTER TABLE `eventjuicer_social_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `one_vote_max` (`participant_id`,`voteable_type`,`voteable_id`,`widget_id`),
  ADD KEY `voteable_type` (`voteable_type`),
  ADD KEY `voteable_id` (`voteable_id`),
  ADD KEY `widget_id` (`widget_id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `organizer_id` (`organizer_id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `eventjuicer_social_votes`
--
ALTER TABLE `eventjuicer_social_votes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;


*/
