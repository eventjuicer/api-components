<?php 

namespace Eventjuicer\Services\Context\ViewComposers;

use Illuminate\Contracts\View\View;

//facade
use Menu;

use Contracts\Context;



/*
* https://github.com/caffeinated/menus/wiki/Filter
*/


class Costapp {

    private $context;
    private $appcontext;

    public function __construct(Context $context)
    {
        $this->context =    $context->level();
        $this->appcontext = $context->app();
    }

    public function compose(View $view)
    {

        $view->with("app_logotype", "/assets/admin/costapp.png");



        if(! $this->appcontext->in_admin())
        {
            return;
        }


         if( $this->context->get("group_id")  )
        {

            $project = $this->context->getParameter("project");

            
            Menu::make('admin', function($menu) use ($project)
            {
                
                $url = 'admin/'.$project.'/documents/';

                $costs = $menu->add('Documents', $url);
                   
                    $costs->add("Recently added", $url . "?orderby=created_at");
                    $costs->add("Issue date", $url . "?orderby=originated_at");
                    $costs->add("Search", $url . "search");
                    $costs->add("Add new",  "admin/documents/create");

                //$config = $menu->add('Settings', $url . "settings");
                  //      $config->add("Templates", $url . "templates");


              


            });   



        }
        else
        {


            Menu::make('admin', function($menu) 
            {

                $url = 'admin/documents/';

                $costs = $menu->add('Documents', $url);
                    $costs->add("Recently added", $url . "?orderby=created_at");
                    $costs->add("Issue date", $url . "?orderby=originated_at");
                    $costs->add("Search", $url . "search");
                    $costs->add("Templates",  "admin/document-templates");
                    $costs->add("Add new", $url . "create");


                // $invoices = $menu->add('Invoices', "admin/invoices");
                // $config = $menu->add('Settings', $url . "settings");
                // $config->add("Templates", $url . "templates");


                  $url = 'admin/imports/';

                  $imports = $menu->add('Imports', $url);
                   
                    $costs->add("New import", $url . "create");
                    



            });   



        }


    }


   



}