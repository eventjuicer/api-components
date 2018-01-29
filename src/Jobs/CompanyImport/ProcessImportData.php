<?php

namespace Eventjuicer\Jobs\CompanyImport;


use Eventjuicer\Jobs\Job;
use Illuminate\Contracts\Queue\ShouldQueue;


use Eventjuicer\Models\CompanyImport;
use Eventjuicer\Repositories\CompanyContactRepository;
use Eventjuicer\Repositories\CompanyContactlistRepository;

//use Eventjuicer\Contracts\Email\Templated as Mailer;
use Exception;
use Carbon\Carbon;

class ProcessImportData extends Job implements ShouldQueue
{


    protected $import;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CompanyImport $import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        CompanyContactRepository $contacts, 
        CompanyContactlistRepository $contactlists
    )
    {   


        $counter = 0;

        $ids = [];
        
        foreach(array_get($this->import->data, "manual", []) as $email)
        {
            $contact = $contacts->makeModel();

            $data   = $contacts->prepare(
                array_merge(
                    $this->import->toArray(), 
                    compact("email"),
                    ["import_id" => $this->import->id]
                )
            );

            if($data)
            {
                $contacts->saveModel($data);

                $ids[] = $contact->id;

                $counter++;

            }
            else
            {
               //we may still want to attach to contactlist?
            }

        }

         
        $contactlists->find($this->import->contactlist_id)->contacts()->syncWithoutDetaching($ids);

        $this->import->imported = $counter;
        $this->import->imported_at = Carbon::now();
        $this->import->save();
       
    }


 
    // public function failed(Exception $exception)
    // {
    //     // Send user notification of failure, etc...
    // }

}
