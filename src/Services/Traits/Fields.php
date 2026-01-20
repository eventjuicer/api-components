<?php

namespace Eventjuicer\Services\Traits;
use Illuminate\Database\Eloquent\Collection;

trait Fields {

    static $event_id = 0;

	public $fields = array(

    "2"=> "fname",
    "3"=> "lname",
    "5"=> "cname",
    "6"=> "industry",
    "7"=> "nip",
    "8"=> "phone",
    "9"=> "company_description",
    "10"=> "company_address",
    "11"=> "cname2",
    "12"=> "duedate",
    "13"=> "amount",
    "14"=> "avatar",
    "15"=> "question1",
    "16"=> "question2",
    "17"=> "question3",
    "18"=> "marketing",
    "19"=> "offer",
    "20"=> "notes",
    "21"=> "presentation_title",
    "22"=> "booth",
    "23"=> "bio",
    "24"=> "position",
    "25"=> "profile_twitter",
    "26"=> "project_description",
    "27"=> "project_url",
    "28"=> "screenshot",
    "29"=> "team_name",
    "30"=> "video",
    "31"=> "slideshare",
    "32"=> "prize_description",
    "33"=> "prize_description2",
    "34"=> "location",
    "35"=> "transport",
    "36"=> "repository",
    "37"=> "technologies",
    "38"=> "project_name",
    "51"=> "team_members",
    "52"=> "skype",
    "53"=> "votes",
    "54"=> "team_description",
    "55"=> "logotype",
    "56"=> "justification",
    "57"=> "ekomers_category",
    "58"=> "presentation_description",
    "59"=> "presentation_time",
    "60"=> "company_website",
    "61"=> "presenter",
    "62"=> "industry_select",
    "63"=> "carpooling_from",
    "64"=> "carpooling_seats",
    "65"=> "carpooling_howto",
    "66"=> "custom_sheepla_platform",
    "67"=> "custom_sheepla_automation",
    "68"=> "custom_sheepla_channels",
    "69"=> "custom_sheepla_customercare",
    "70"=> "custom_sheepla_logistics",
    "71"=> "custom_sheepla_jobtype",
    "72"=> "custom_sheepla_developer",
    "73"=> "custom_orba_platform",
    "74"=> "custom_orba_offline2online",
    "75"=> "custom_orba_automation",
    "76"=> "custom_orba_shipping",
    "77"=> "custom_orba_developer",
    "78"=> "custom_codemedia_salesrep",
    "79"=> "custom_codemedia_channels",
    "80"=> "custom_codemedia_activities",
    "81"=> "custom_jury_innovation",
    "82"=> "custom_codemedia_popularity",
    "83"=> "contest_cname2",
    "84"=> "ekomers2014_category",
    "85"=> "product_name",
    "86"=> "school",
    "87"=> "system_booth_number",
    "88"=> "system_booth_sysid",
    "89"=> "terms_and_conditions",
    "90"=> "custom_admin_1",
 
    "93"=> "choose_presentation_slot",
    "94"=> "skad_wiesz_o_certyfikacji",
    "95"=> "visitorinfo",
    "96"=> "registration_source",
    "97"=> "exam_participation",
    "98"=> "presentation_venue",
    "99"=> "tm_interests",
    "100"=> "tm_promo",
    "101"=> "tm_visitday",
    "102"=> "logotype_vector",
    "103"=> "avatar_vector",
    "104"=> "presentation_day",
    "105"=> "obszary_wiedzowe_eksperta",
    "106"=> "project",
    "107"=> "company_start",
    "108"=> "workforce",
    "109"=> "short_description_300",
    "110"=> "short_description_person_300",
    "111"=> "area_of_activity",
    "112"=> "case_studies_links",
    "113"=> "communication_channels",
    "114"=> "logistics_target",
  
    
    "122"=> "fb_thumbnail",
    "123"=> "email2",
    "124"=> "fname2",
    "125"=> "lname2",
    "126"=> "justification_jury",
    "127"=> "presentation_title_proposal",
    "128"=> "kod_promocyjny",
    "129"=> "location_is_warsaw",
    "130"=> "phone2",
    "131"=> "poll_essential",
    "132"=> "poll_organizational",
    "133"=> "poll_organizational_tips",
    "134"=> "poll_in_plus",
    "135"=> "poll_communication",
    "136"=> "poll_attendance",
    "137"=> "video_teaser",
   
    
    "174"=> "custom_jury_payment_fintech",
    "175"=> "custom_jury_analytics_bi",
    "176"=> "custom_jury_agency",
    "177"=> "custom_jury_it",
    "178"=> "sales",
    "179"=> "communication",
    "180"=> "expansion",
    "181"=> "logistics",
    "182"=> "ekomersy_participants",
    "183"=> "e_mail",
    "184"=> "email3",
    "185"=> "lname3",
    "186"=> "fname3",
    "187"=> "presenter_tags",
    "188"=> "get_info",
    "189"=> "product_description",
    "190"=> "profile_facebook",
    "191"=> "externalprofile",
    "192"=> "next_venue",
    "193"=> "catering_upgrade",
    "194"=> "visit_intention",
    "195"=> "visit_position",
    "196"=> "visit_department",
    "197"=> "ad_source_url",
    "198"=> "skills",
    "199"=> "presenter_publication",
    "200"=> "product_benefits",
    "201"=> "product_showcase_url",
    "202"=> "product_url",
    "203"=> "ekomersy_merchants_socialstrategy",
    "204"=> "ekomersy_merchants_videomarketing",
    "205"=> "ekomersy_merchants_omnichannel",
    "206"=> "ekomersy_merchants_mobilestrategy",
    "207"=> "ekomersy_merchants_loyalty",
    "208"=> "ekomersy_merchants_international",
    "209"=> "ekomersy_person",
    "210"=> "ekomersy_person_innovator",
    "211"=> "profile_linkedin",
    "212"=> "ekomersy_person_company",
    "213"=> "votes_override",
    "214"=> "platform",
    "215"=> "payment",
    "216"=> "analytics",
    "217"=> "agency",
    "218"=> "custom_jury_sales",
    "219"=> "custom_jury_communication_engagement",
    "220"=> "additional_jury_info",
    "221"=> "custom_jury_global_expansion",
    "222"=> "custom_jury_logistics_delivery",
    "223"=> "custom_jury_platforms_tools",
    "224"=> "ekomersy_juror_rsvp",
    "225"=> "poll_organizational_expo",
    "226"=> "cfp_category",
    "227"=> "poll_general",
    "228"=> "xing_profile",
    "229"=> "cfp_category_eb",
    "230"=> "additional_info",
    "231"=> "poll_paid_visiting",
    "232"=> "poll_mobile_app",
    "233"=> "poll_konferansjer",
    "234"=> "poll_interaction",
    "235"=> "poll_welcome_pack",
    "236"=> "awards_category",
    "237"=> "innovations",
    "238"=> "difference",
    "239"=> "printed_logotype",
    "240"=> "budget",
    "241"=> "nationality",
    "242"=> "employee",
    "243"=> "poll_retention",
    "244"=> "electricity_upgrade",
    "245"=> "chairs_upgrade",
    "246"=> "table_upgrade",
    "247"=> "monitor_rent_upgrade",
    "248"=> "stand_upgrade",
    "249"=> "covering_upgrade",
    "250"=> "featured",
    "251"=> "email4",
    "252"=> "carpet_color",
    "253"=> "presentation_category",
    "254"=> "avatar_cdn",
    "255"=> "logotype_cdn",
    "256"=> "votes_earned",
    "257"=> "account",
    "258"=> "important",
    "259"=> "referral",
    "260"=> "winner",
    "261"=> "featured_cfp",
    "262"=> "testimonials",
    "263"=> "case_study",
    "264"=> "confidential",
    "265"=> "video_length_minutes",
    "266"=> "url",
    "267"=> "video_is_public",
    "268"=> "locale",
    "269"=> "accept",
    "270"=> "custom_jury_multichannel_marketplace",
    "271"=> "custom_jury_omnichannel_integration",
    "272"=> "code",
    "273"=> "limited",
    "274"=> "company_role", //old 86
    "275"=> "participant_type", //old 24
    "276"=> "party_participant_type",
    "277"=> "custom_jury_cx_personalization",
    "278"=> "custom_jury_fulfillment_optimization",
    "279"=> "business_model",
    "280"=> "objective",
    "281"=> "revenue",
    "282"=> "presenter_fname",
    "283"=> "presenter_lname",
    "284"=> "invoice_street",
    "285"=> "invoice_postcode",
    "286"=> "invoice_city",
    "287"=> "invoice_country",
    "288"=> "url_local",
    "289"=> "parent_uuid",
    "290"=> "ticket_internal_id",
    "291"=> "adoption_metrics",
    "292"=> "contestant_company_profile_summary"

  );


 
    public function getFieldName($keyId){
        return array_get($this->fields, $keyId, null);
    }

    public function getFieldId($keyName){
        return array_get(
            array_flip($this->fields), 
            $keyName, 
            null
        );
    }

    public function filterFields(Collection $profile, $showable = [], $conditions = []){

        $filtered = $profile->mapWithKeys(function($item)use($showable) {

           // $key = array_search($item->field_id, $this->presenterFields);

            $key = $this->getFieldName($item->field_id);

            if($key && in_array($key, $showable)){
                return [$key => $item->field_value];
            }

            return [];
           })->all();

        //conditional filtering

        if(!empty($conditions)){
            
        }

        return $filtered;
    }

}

 

