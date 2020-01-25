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
            "linkedin" => "https://www.linkedin.com/shareArticle?mini=true&url=".$url,
            "twitter" => "https://twitter.com/home?status=" . $url
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
        $opengraph_image_cdn = (string) (new CloudinaryImage( array_get($cd, "opengraph_image_cdn", "")))->version();


        //EBE!
        if( $this->companydata->getCompany()->organizer_id > 1) 
        {

            $logotype_cdn_en = $logotype_cdn->wrapped("ebe5_template_en");
            $logotype_cdn_de = $logotype_cdn->wrapped("ebe5_template_de");

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
                "link" => $this->companydata->trackingLink("link", "logotype,en"),
                "shareable" => true,
                "services" => ["linkedin", "twitter", "facebook"],
                "requires" => ["logotype"],
                "enabled" => strpos(array_get($cd, "logotype_cdn", ""), "http") !== false,
                "template" => $logotype_cdn_en,
                "sharers" => $this->sharer( $this->companydata->trackingLink("link", "logotype,en") )
            
            ],

            [
                "id" => 51,
                "name" => "logotype",
                "lang" => "de",
                "act_as" => "link",
                "link" =>  $this->companydata->trackingLink("link", "logotype,de"),
                "shareable" => true,
                "services" => ["facebook"],
                "requires" => ["logotype"],
                "enabled" => strpos(array_get($cd, "logotype", ""), "http") !== false,
                "template" => $logotype_cdn_de
            
            ],


            [
                "id" => 52,
                "name" => "opengraph_image",
                "lang" => "undefined",
                "act_as" => "link",
                "link" =>  $this->companydata->trackingLink("link", "opengraph_image"),
                "shareable" => true,
                "services" => ["facebook"],
                "requires" => ["opengraph_image"],
                "enabled" => strpos( array_get($cd, "opengraph_image_cdn", ""), "http") !== false,
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

            $logotype_cdn_pl = $logotype_cdn->wrapped("template_teh19_exhibitor_pl");
            $logotype_cdn_en = $logotype_cdn->wrapped("template_teh19_exhibitor_en");

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
                "lang" => "pl",
                "act_as" => "link",
                "link" =>  $this->companydata->trackingLink("link", "logotype,pl"),
                "shareable" => true,
                "services" => ["linkedin", "twitter", "facebook"],
                "requires" => ["logotype"],
                "enabled" => strpos(array_get($cd, "logotype", ""), "http") !== false,
                "template" => $logotype_cdn_pl,
                "sharers" => $this->sharer( $this->companydata->trackingLink("link", "logotype,pl") )
                //"template" => 'https://res.cloudinary.com/eventjuicer/image/upload/c_fit,g_center,h_220,w_600,y_30,l_c_'.$company_id.'_logotype/ebe_template_en.png'
            ],

            [
                "id" => 51,
                "name" => "logotype",
                "lang" => "en",
                "act_as" => "link",
                "link" =>  $this->companydata->trackingLink("link", "logotype,en"),
                "shareable" => true,
                "services" => ["facebook"],
                "requires" => ["logotype"],
                "enabled" => strpos(array_get($cd, "logotype", ""), "http") !== false,
                "template" => $logotype_cdn_en,
                "sharers" => $this->sharer( $this->companydata->trackingLink("link", "logotype,en") )
                //"template" => 'https://res.cloudinary.com/eventjuicer/image/upload/c_fit,g_center,h_220,w_600,y_30,l_c_'.$company_id.'_logotype/ebe_template_en.png'
            
            ],


            [
                "id" => 52,
                "name" => "opengraph_image",
                "lang" => "undefined",
                "act_as" => "link",
                "link" =>  $this->companydata->trackingLink("link", "opengraph_image"),
                "shareable" => true,
                "services" => ["facebook"],
                "requires" => ["opengraph_image"],
                "enabled" => strpos( array_get($cd, "opengraph_image", ""), "http") !== false,
                "template" => 'https://res.cloudinary.com/eventjuicer/image/upload/w_960,h_504,c_fit/' . $opengraph_image_cdn,
                "sharers" => $this->sharer( $this->companydata->trackingLink("link", "opengraph_image") )
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