<?php 

namespace Eventjuicer\Services\Context\ViewComposers;

use Illuminate\Contracts\View\View;

//facade
use Menu;



use Contracts\Context;


/*
* https://github.com/caffeinated/menus/wiki/Filter
*/


class Eventjuicer {


    protected $context;
    private $appcontext;

    public function __construct(Context $context)
    {
        $this->context = $context->level();
        $this->appcontext = $context->app();
    }



    

    public function compose(View $view)
    {


        $view->with("app_logotype", "/assets/admin/layout5/img/logo.png");


        if(! $this->appcontext->in_admin())
        {
            return;
        }

        

        if( $this->context->get("event_id") )
        {

            $project = $this->context->getParameter("project");

            $event_id = $this->context->get("event_id");  


            Menu::make('admin', function($menu) use ($project, $event_id)
            {

                $url = 'admin/'. $project . "/" .  $event_id;     

                $menu->add('Dashboard', $url);
               
                $menu->dashboard->add("Costam" , "costamlink");

               
                $menu->add('Sales',  $url . '/purchases');
           
                $menu->add('Registrations', $url . '/participants');


                $config = $menu->add('Config', $url . '/config');

                $config->add("Settings", $url . "/settings");


                $config->add("Automation", $url . "/automation");


            });

        }
        else if( \Context::level()->get("group_id")  )
        {

            $project = \Context::level()->getParameter("project");

          
            Menu::make('admin', function($menu) use ($project)
            {

                $url = 'admin/'. $project;

                $menu->add('Dashboard', $url );

                $menu->add('Sales', $url . "/purchases");

                $menu->add('Registrations', $url .'/participants');

                $config = $menu->add('Manage', '#');

                    $config->add('Settings', $url . '/settings');
                    $config->add('Texts',    $url . '/texts');
                    $config->add('Contexts', $url . '/contexts');
                    $config->add('Domains',   $url . "/domains" );
                    $config->add('Sales',   $url . "/domains" );

            });
    


           
        }
        else
        {

            //organizer
            Menu::make('admin', function($menu)
            {

                $url = "admin";

                $menu->add('Dashboard', 'admin');
                $menu->dashboard->add("Costam" , "costamlink");

                $menu->add('Registrations', 'admin/participants');

                $sales = $menu->add('Sales', "javascript:;");
             
                $sales->add("Dashboard", 'admin/purchases');
                $filtered = $sales->add("Status: ...", "javascript:;");
                $filtered->add("test", "admin/purchases?tagged=");


                $leads = $menu->add('Leads', 'javascript:;');

                    $leads->add("Dashboard", $url . "/sender");

                    
                    $campaigns = $leads->add("Campaigns", $url . "/sender/campaigns")->active('admin/sender/campaigns/*');
                    $campaigns->add("New",   $url . "/sender/campaigns/create");


                    $newsletter = $leads->add("Newsletters", $url . "/sender/newsletters")->active('admin/sender/newsletters/*');
                    $newsletter->add("New", $url . "/sender/newsletters/create");


                    $lists = $leads->add("Lists", $url . "/sender/imports")->active('admin/sender/imports/*');
                    $lists->add("New", $url . "/sender/imports/create");


                    $leads->add("Subscribers", $url . "/sender/emails");



                 $config = $menu->add('Config', '#');


                    $config->add('Organizer', 'admin/organizer');
                    $config->add('Settings', 'admin/settings');
                    $config->add('Texts',    'admin/texts');
                    $config->add('Contexts', 'admin/contexts');
                    $config->add('Users',    'admin/users');


                    $config->add('Projects', '#');
  
                    $config->add('Events', 'admin/eventgroups');
                    $config->add('Portals', 'admin/portals');


                    $config->add('Domains',   $url . "/domains" );

            });
        }




    }





    

}