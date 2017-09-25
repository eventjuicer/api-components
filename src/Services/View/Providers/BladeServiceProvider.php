<?php


namespace Eventjuicer\Services\View\Providers;

use Illuminate\Support\ServiceProvider;

use Blade;

use Contracts\Context;


class BladeServiceProvider extends ServiceProvider
{



    public function boot(Context $context)
    {



        if(! $context->app()->in_admin())
        {   
           $this->publicBlade();
        }



        Blade::directive('app', function($arguments = "") 
        {
           return "<?php if( \Context::app()->is$arguments ): ?>";
        });

        Blade::directive('endapp', function($arguments = "") 
        {
           return "<?php endif; ?>";
        });

        
        Blade::directive('assets', function($arguments) 
        {
           return "<?php echo \Assets::load$arguments ?>";
        });

        Blade::directive('asset', function($arguments) 
        {
           return "<?php echo \Assets::load$arguments ?>";
        });

        Blade::directive('template', function($arguments) 
        {
           return "<?php echo \Template::get$arguments;?>";
        });



        Blade::directive('pageheader', function($arguments) 
        {
           return "<?php echo \Template::pageheader$arguments; ?>";
        
        });


    }

    
    private function publicBlade()
    {




        /* START Editorapp - related */

        Blade::directive('person', function($arguments) 
        {
           return "<?php echo \Parse::head$arguments ?>";
        });


        Blade::directive('company', function($arguments) 
        {
           return "<?php echo \Parse::head$arguments ?>";
        });

        Blade::directive('tag', function($arguments) 
        {
           return "<?php echo \Parse::head$arguments ?>";
        });

        Blade::directive('post', function($arguments) 
        {
           return "<?php echo \Parse::head$arguments ?>";
        });


        /* END Editorapp - related */



        Blade::directive('video', function($arguments) 
        {
           return "<?php echo \Parse::video$arguments ?>";
        });


        Blade::directive('image', function($arguments) 
        {
           return "<?php echo \Parse::image$arguments ?>";
        });


        Blade::directive('button', function($arguments) 
        {
           return "<?php echo \Parse::button$arguments ?>";
        });



        Blade::directive('widget', function($arguments)
        {       
            return "<?php echo \Parse::widget$arguments; ?>";
            
        });

        
        Blade::directive('setting', function($arguments)
        {
             return "<?php echo \Setting::get$arguments; ?>";

        });

        Blade::directive('text', function($arguments)
        {
             return "<?php echo \Text::get$arguments; ?>";

        });


    }


    public function register()
    {
       
        
     

    }


   


}