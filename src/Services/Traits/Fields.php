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
    "91"=> "ekomersy2017_jury_description",
    "92"=> "ekomersy2017_jury_url",
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
    "115"=> "ekomersy_person_achivement",
    "116"=> "ekomersy_clients",
    "117"=> "ekomersy_innovations",
    "118"=> "ekomersy_idea",
    "119"=> "ekomersy_idea_differences",
    "120"=> "ekomersy_idea_differences2",
    "121"=> "ekomersy_idea_difference2",
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
    "138"=> "ekomersy2016_key_innovation",
    "139"=> "ekomersy2016_integrations_payments",
    "140"=> "ekomersy2016_integrations_logistics",
    "141"=> "ekomersy2016_growth_rate",
    "142"=> "ekomersy2016_avg_monthly_fee",
    "143"=> "ekomersy2016_non_technical_support",
    "144"=> "ekomersy2016_omnichannel_support",
    "145"=> "ekomersy2016_payments_security",
    "146"=> "ekomersy2016_payments_noncc",
    "147"=> "ekomersy2016_mcommerce_support",
    "148"=> "ekomersy2016_international_sales_support",
    "149"=> "ekomersy2016_integrations_solutions",
    "150"=> "ekomersy2016_market_reach",
    "151"=> "ekomersy2016_advanced_features",
    "152"=> "ekomersy2016_problem_solved",
    "153"=> "ekomersy2016_value_consumer",
    "154"=> "ekomersy_value_implementer",
    "155"=> "ekomersy2016_5_biggest_clients",
    "156"=> "ekomersy2016_promo_goal",
    "157"=> "ekomersy2016_promo_target_group",
    "158"=> "ekomersy2016_promo_activities",
    "159"=> "ekomersy2016_promo_cpm",
    "160"=> "ekomersy2016_promo_theme",
    "161"=> "ekomersy2016_promo_effects",
    "162"=> "ekomersy2016_mobile_socialmedia_support",
    "163"=> "ekomersy2016_mobile_internal_support",
    "164"=> "ekomersy2016_mobile_shopping_enhancement",
    "165"=> "ekomersy2016_salesgeneration_cac",
    "166"=> "ekomersy2016_implementation_changes",
    "167"=> "ekomersy2016_implementation_effects",
    "168"=> "ekomersy2016_international_costeffectiveness",
    "169"=> "ekomersy2016_customersupport_clv",
    "170"=> "ekomersy2016_customersupport_implementation",
    "171"=> "ekomersy2016_customersupport_integration",
    "172"=> "ekomersy2017_jury_contact",
    "173"=> "ekomersy2017_gala_rsvp",
    "174"=> "custom_jury_payments",
    "175"=> "custom_jury_analytics",
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
    "219"=> "custom_jury_communication",
    "220"=> "additional_jury_info",
    "221"=> "custom_jury_expansion",
    "222"=> "custom_jury_logistics",
    "223"=> "custom_jury_platform",
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
    
    "272"=> "vipcode"

  );


    public function getFieldName($keyId){
        return array_get($this->fields, $keyId, null);
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

 

