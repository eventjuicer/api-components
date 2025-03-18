<?php 

namespace Eventjuicer\Services\Exhibitors;

use Illuminate\Database\Eloquent\Model;
use Eventjuicer\Services\Exhibitors\CompanyData;
use Eventjuicer\ValueObjects\CloudinaryImage;
use Eventjuicer\ValueObjects\Url;

class Creatives {

	protected $companydata = null;
    protected $services = ["linkedin", "twitter", "facebook"];

	function __construct(CompanyData $companydata){

        $this->companydata = $companydata;
    }



    public function sharer(string $url, array $config = []){

        $url = (new Url($url))->encodeURIComponent();
     
        return [

            "facebook" => "https://www.facebook.com/sharer/sharer.php?u=".$url,
            "linkedin" => "https://www.linkedin.com/sharing/share-offsite/?url=".$url,
            "twitter" => "https://twitter.com/intent/tweet?text=" . $url
        ];
    }

    public function newsletter(string $url){

        return [

            "html" => $url."?participant_id=".$this->companydata->id."&dl=1",
            "zip" =>  $url."?participant_id=".$this->companydata->id."&zip=1"
        ];
    }

	public function get( ){

        $cd = $this->companydata->companyData();


        $logotype_cdn = new CloudinaryImage( array_get($cd, "logotype_cdn", ""));
        $opengraph_image_cdn = (string) (new CloudinaryImage( array_get($cd, "opengraph_image_cdn", "")))->thumb(1200, 630);


        $hasCustomImage = strpos( array_get($cd, "opengraph_image", ""), "http") !== false &&strpos( array_get($cd, "opengraph_image_cdn", ""), "cloudinary") !== false;

        $hasBasicLogo = strpos(array_get($cd, "logotype", ""), "http") !== false && strpos(array_get($cd, "logotype_cdn", ""), "cloudinary") !== false;

        //EBE!
        if( $this->companydata->getCompany()->organizer_id > 1) 
        {

            $lang = trim(strval(array_get($cd, "lang"))) ? array_get($cd, "lang") : "en";


            $logotype_cdn_en = $logotype_cdn->wrapped("ebe5_2__template_en");
            $logotype_cdn_de = $logotype_cdn->wrapped("ebe5_2__template_de");

            //BERLIN!
            
            return [

            [  
                "id" => 1,
                "name"=> "invite",
                "lang" => "de",
                "act_as"=>"newsletter" ,
                "content"=>"https://services.eventjuicer.com/api/company-newsletters/1",
                "newsletter" => $this->newsletter("https://services.eventjuicer.com/api/company-newsletters/1")
               
            ],

            [  
                "id" => 2,
                "name"=> "invite",
                "lang" => "en",
                "act_as"=>"newsletter" ,
                "content"=>"https://services.eventjuicer.com/api/company-newsletters/2",
                "newsletter" => $this->newsletter("https://services.eventjuicer.com/api/company-newsletters/2")
               
            ],


            [
                "id" => 50,
                "name" => "logotype",
                "lang" => "en",
                "act_as" => "link",
                "link" => $this->companydata->trackingLink("link", "logotype"),
                "link_full" => $this->companydata->trackedProfileUrl("link", "logotype"),
                "shareable" => true,
                "services" => ["linkedin", "twitter", "facebook"],
                "requires" => ["logotype"],
                "enabled" => $hasBasicLogo && !$hasCustomImage,
                "template" => $logotype_cdn_en,
                "sharers" => $this->sharer( $this->companydata->trackedProfileUrl("link", "logotype") )
            
            ],

      

            

            // [
            //     "id" => 51,
            //     "name" => "logotype",
            //     "lang" => "de",
            //     "act_as" => "link",
            //     "link" =>  $this->companydata->trackingLink("link", "logotype,de"),
            //     "link_full" => $this->companydata->trackedProfileUrl("link", "logotype,de"),
            //     "shareable" => true,
            //     "services" => ["facebook"],
            //     "requires" => ["logotype"],
            //     "enabled" => $hasBasicLogo && !$hasCustomImage,
            //     "template" => $logotype_cdn_de,
            //     "sharers" => $this->sharer( $this->companydata->trackedProfileUrl("link", "logotype,de") )
            
            // ],


            [
                "id" => 52,
                "name" => "opengraph_image",
                "lang" => "undefined",
                "act_as" => "link",
                "link" =>  $this->companydata->trackingLink("link", "custom"),
                "link_full" => $this->companydata->trackedProfileUrl("link", "custom"),
                "shareable" => true,
                "services" => ["facebook"],
                "requires" => ["opengraph_image"],
                "enabled" => $hasCustomImage,
                "template" => 'https://res.cloudinary.com/eventjuicer/image/upload/w_960,h_504,c_fit/' . $opengraph_image_cdn
            
            ],

            /*

            1200px x 630px - post FB & LinkedIn
            820px x 340px - business cover FB
            1536px x 768px - business cover LinkedIn
            1500px x 500px - cover Twitter 
            650px x 120px - email signature

            */
            /*
            [
                "id" => 100,
                "name"=>"1536 x 768",
                "lang" => "pl",
                "act_as"=>"image" ,
                "keywords" => ["linkedin_cover"],
                "image"=>"https://res.cloudinary.com/eventjuicer/image/upload/v1523638084/banner_pl_1536x768.png",
                "link" =>  $this->companydata->trackingLink("banner", "banner_pl_1536x768")
            ],
            
            [
                "id" => 101,
                "name"=>"1500 x 500",
                "lang" => "pl",
                "act_as"=>"image" ,
                "keywords" => ["twitter_cover"],
                "image"=>"https://res.cloudinary.com/eventjuicer/image/upload/v1523638083/banner_pl_1500x500.png",
                "link" =>  $this->companydata->trackingLink("banner", "banner_pl_1500x500")
            ],
            
            [
                "id" => 102,
                "name"=>"1200 x 630",
                "lang" => "pl",
                "act_as"=>"image" ,
                "keywords" => ["facebook_post", "linkedin_post"],
                "image"=>"https://res.cloudinary.com/eventjuicer/image/upload/v1523638082/banner_pl_1200x630.png",
                 "link" =>  $this->companydata->trackingLink("banner", "banner_pl_1200x630")
            ],
            
            [   
                "id" => 103,
                "name"=>"400 x 150",
                "lang" => "pl",
                "act_as"=>"image" ,
                "keywords" => ["facebook", "linkedin"],
                "image"=>"https://res.cloudinary.com/eventjuicer/image/upload/v1523638082/banner_pl_400x150.png",
                "link" =>  $this->companydata->trackingLink("banner", "banner_pl_400x150")
            //     "shareable" => true

            ],
            
       
            */
           

            ];


        }else{

            //TEH

            $lang = trim(strval(array_get($cd, "lang"))) ? array_get($cd, "lang") : "pl";

            $logotype = $logotype_cdn->wrapped("template_teh27_exhibitor_" . $lang);


            return [

            [  
                "id" => 1,
                "name"=> "invite",
                "lang" => "pl",
                "act_as"=>"newsletter" ,
                "content"=>"https://services.eventjuicer.com/api/company-newsletters/100",
                "newsletter" => $this->newsletter("https://services.eventjuicer.com/api/company-newsletters/100")
               
            ],

            [  
                "id" => 2,
                "name"=> "invite",
                "lang" => "en",
                "act_as"=>"newsletter" ,
                "content"=>"https://services.eventjuicer.com/api/company-newsletters/101",
                "newsletter" => $this->newsletter("https://services.eventjuicer.com/api/company-newsletters/101")
               
            ],


            [
                "id" => 50,
                "name" => "logotype",
                "lang" => array_get($cd, "lang", "pl"),
                "act_as" => "link",
                "link" =>  $this->companydata->trackingLink("link", "logotype"),
                "link_full" => $this->companydata->trackedProfileUrl("link", "logotype"),
                "shareable" => true,
                "services" => ["linkedin", "twitter", "facebook"],
                "requires" => ["logotype"],
                "enabled" =>  $hasBasicLogo && !$hasCustomImage,
                "template" => $logotype,
                "sharers" => $this->sharer( $this->companydata->trackedProfileUrl("link", "logotype") )
            ],


            [
                "id" => 51,
                "name" => "opengraph_image",
                "lang" => "undefined",
                "act_as" => "link",
                "link" =>  $this->companydata->trackingLink("link", "opengraph_image"),
                "link_full" => $this->companydata->trackedProfileUrl("link", "opengraph_image"),
                "shareable" => true,
                "services" => ["facebook", "linkedin", "twitter"],
                "requires" => ["opengraph_image"],
                "enabled" => $hasCustomImage,
                "template" => $opengraph_image_cdn,
                "sharers" => $this->sharer( $this->companydata->trackedProfileUrl("link", "opengraph_image") )
            ],

            /*

            1200px x 630px - post FB & LinkedIn
            820px x 340px - business cover FB
            1536px x 768px - business cover LinkedIn
            1500px x 500px - cover Twitter 
            650px x 120px - email signature

            */

            /*

            [
                "id" => 100,
                "name"=>"1536 x 768",
                "lang" => "pl",
                "act_as"=>"image" ,
                "keywords" => ["linkedin_cover"],
                "image"=>"https://res.cloudinary.com/eventjuicer/image/upload/v1523638084/banner_pl_1536x768.png",
                "link" => $this->companydata->trackingLink("banner", "banner_pl_1536x768")
            ],
            
            [
                "id" => 101,
                "name"=>"1500 x 500",
                "lang" => "pl",
                "act_as"=>"image" ,
                "keywords" => ["twitter_cover"],
                "image"=>"https://res.cloudinary.com/eventjuicer/image/upload/v1523638083/banner_pl_1500x500.png",
                "link" =>  $this->companydata->trackingLink("banner", "banner_pl_1500x500")
            ],
            
            [
                "id" => 102,
                "name"=>"1200 x 630",
                "lang" => "pl",
                "act_as"=>"image" ,
                "keywords" => ["facebook_post", "linkedin_post"],
                "image"=>"https://res.cloudinary.com/eventjuicer/image/upload/v1523638082/banner_pl_1200x630.png",
                 "link" =>  $this->companydata->trackingLink("banner", "banner_pl_1200x630")
            ],
            
            [   
                "id" => 103,
                "name"=>"400 x 150",
                "lang" => "pl",
                "act_as"=>"image" ,
                "keywords" => ["facebook", "linkedin"],
                "image"=>"https://res.cloudinary.com/eventjuicer/image/upload/v1523638082/banner_pl_400x150.png",
                "link" =>  $this->companydata->trackingLink("banner", "banner_pl_400x150")
            //     "shareable" => true

            ],
            
            */
       
           

            ];

        }




	}
	
}