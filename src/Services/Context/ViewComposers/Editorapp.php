<?php 

namespace Eventjuicer\Services\Context\ViewComposers;

use Illuminate\Contracts\View\View;

//facade
use Menu;


use Contracts\Context;



/*
* https://github.com/caffeinated/menus/wiki/Filter
*/


class Editorapp {


    private $context;
    private $appcontext;
    private $user;

    public function __construct(Context $context)
    {
        $this->context = $context->level();
        $this->appcontext = $context->app();
        $this->user = $context->user();

    }
    

    public function compose(View $view)
    {

      

      $view->with("app_logotype", "/assets/admin/layout5/img/logo.png");

      
       if( $this->context->get("group_id")  )
        {

            $project = $this->context->getParameter("project");

            
             Menu::make('admin', function($menu) use ($project)
            {

                $url = 'admin/'. $project;

               // $menu->add('Dashboard', "admin/" . $project );


                $this->posts($menu, $url);



               
           //     $menu->add('Newsdesk',      $url . "/newsdesk" );      
             //   $menu->add('Leads',         $url . "/leads" );
               // $menu->add('Automation',    $url . "/automation" );

                $config = $menu->add('Config', 'javascript:;');

                    $config->add('Contexts', 'admin/contexts');

                    $config->add('Settings', $url . '/settings');
                    $config->add('Texts',    $url . '/texts');
                 ///   $config->add('Contexts', $url . '/contexts');
                    $config->add('Topics',   $url . "/topics" );
                
                $config->add('Domains',   $url . "/domains" );
                 $config->add('Pages',   $url . "/pages" );

                   // $config->add('Widgets',   $url . "/widgets" );


            });

           
        }
        else
        {

            //organizer
            Menu::make('admin', function($menu)
            {

                $url = "admin";

               // $menu->add('Dashboard', 'admin');

                $this->posts($menu, $url);


                $menu->add('Media', 'javascript:;');
                $menu->add('Marketing', 'javascript:;');
                $menu->add('Newsroom', 'javascript:;');


            //    $menu->add('Newsdesk',      $url . "/newsdesk" );      
              //  $menu->add('Leads',         $url . "/leads" );
                //$menu->add('Automation',    $url . "/automation" );


                 $config = $menu->add('Config', "javascript:;");


                    $config->add('Settings', 'admin/settings');
                    $config->add('Texts',    'admin/texts');
                  ///  $config->add('Contexts', 'admin/contexts');
                   

                  
                    $config->add('Pages',   $url . "/pages" );

                    $config->add('Widgets',   $url . "/widgets" );


                  $other =   $config->add('Other',   "javascript:;" );
                  $other->add('Contexts', 'admin/contexts');
                  $other->add('Domains',   $url . "/domains" );


            });
        }


    }




    private function posts($menu, $url)
    {
                
        $posts = $menu->add('Posts', $url . "/posts?sortby=created_at");

        $posts->add('Planned',               $url . "/posts?conditions[is_published]=2&sortby=published_at" );
        $posts->add('Drafts',                $url . "/posts?conditions[is_published]=0&sortby=updated_at" );
        $posts->add('Published',            $url . "/posts?sortby=published_at&conditions[is_published]=1" );
        
        $posts->add('By interactivity',      $url . "/posts?sortby=interactivity" );

        $flags = $posts->add('Other filters',      "" );

            
            

            $flags->add('Sorted - created',  $url . "/posts?sortby=created_at" );
            $flags->add('Sorted - edited',    $url . "/posts?sortby=updated_at" );

            $flags->add('Promoted',          $url . "/posts?conditions[is_promoted]=1" );
            $flags->add('Sticky',            $url . "/posts?conditions[is_sticky]=1" );
            $flags->add('Unassigned',        $url . "/posts?conditions[group_id]=0" );
            
            
            $flags->add('Mine',              $url . "/posts?conditions[admin_id]=" . $this->user->id() );
        //    $flags->add('Fix me!',           $url . "/posts?conditions[has_errors]=1" );


    }

}