<?php

namespace Eventjuicer\Jobs\CompanyImport;


use Eventjuicer\Jobs\Job;
use Illuminate\Contracts\Queue\ShouldQueue;


use Eventjuicer\Models\CompanyImport;
use Eventjuicer\Repositories\CompanyContactRepository;
use Eventjuicer\Repositories\CompanyContactlistRepository;
use Eventjuicer\ValueObjects\Phone;


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


        $ids = [];
        
        foreach(array_get($this->import->data, "manual", []) as $email)
        {   

            $email = trim($email, " ,");
        
            $saved = $this->saveContact($contacts, compact("email"));

            if($saved)
            {
                $ids[] = $saved;
            }

        }


        foreach(array_get($this->import->data, "csv", []) as $row)
        {
            $email = array_get($row, "email", false);

            if($email)
            {
                unset($row["email"]);
            }

            if(isset($row["phone"]))
            {
                $row["phone"] = (string) (new Phone($row["phone"]));
            }
            
            $saved = $this->saveContact($contacts, ["email" => $email, "data" => $row]);

            if($saved)
            {
                $ids[] = $saved;
            }

        }

         
        $contactlists->find($this->import->contactlist_id)->contacts()->syncWithoutDetaching($ids);

        $this->import->imported = count($ids);
        $this->import->imported_at = Carbon::now();
        $this->import->save();
       
    }

    protected function saveContact(CompanyContactRepository $contacts, $postData)
    {

        $contact = $contacts->makeModel();

        $data   = $contacts->prepare(
                array_merge(
                    $this->import->toArray(), 
                    $postData,
                    ["import_id" => $this->import->id]
                )
        );

        if($data)
        {
            $contacts->saveModel($data);

            return $contact->id;
        }
        
        return false;

    }

 
    // public function failed(Exception $exception)
    // {
    //     // Send user notification of failure, etc...
    // }

}
