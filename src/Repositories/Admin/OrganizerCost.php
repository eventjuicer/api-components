<?php  


namespace Repositories\Admin;

use Bosnadev\Repositories\Contracts\RepositoryInterface;

//use Bosnadev\Repositories\Eloquent\Repository;

use Services\Repository;

use Eventjuicer\Cost;
use Eventjuicer\Tag;

use Context;

use DB;
use App;

//https://bosnadev.com/2015/03/26/using-repository-pattern-in-laravel-5-eloquent-relations-and-eager-loading/

class OrganizerCost extends Repository
{


    protected $preventCriteriaOverwriting = false;


	public function model()
	{
        return Cost::class;
    }


  
    public function mostUsedTags($costam="")
    {

       //dd(Cache::remember());

        return $this->cached(null, 15, function()
        {
            return $this->model->mostUsedTags();
        });

    }

    public function tags($id = 0)
    {
    	return $this->find($id)->tags;
    }


    public function monthly()
    {
        return Cost::select(["*", \DB::raw("SUM(amount) as total"), \DB::raw("DATE_FORMAT(originated_at, '%Y%m') as _index")])->where("organizer_id", Context::level()->get_organizer_id())->groupby("_index")->orderby("originated_at", "DESC")->get();

    }


    public function create(array $data)
    {

    	$cost = new Cost;

    	$cost->organizer_id = Context::level()->get_organizer_id();

        $cost->fill($data)->save();

        return $cost;

    }

	public function update(array $data, $id)
	{
    	//return true;

    	$model = $this->find($id);

    	$model->organizer_id = Context::level()->get_organizer_id();

        return $model->fill($data)->save();
    
    }


}