<?php  namespace Repositories\Admin;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;


//custom

use Contracts\Context;



use Eventjuicer\PostImage;

use Eventjuicer\Tag;

use DB;
use App;

//https://bosnadev.com/2015/03/26/using-repository-pattern-in-laravel-5-eloquent-relations-and-eager-loading/

class ImageRepository extends Repository
{

    protected $usercontext;
    protected $context;

    function __construct(Context $context)
    {

        $this->context      = $context->level();
        $this->usercontext  = $context->user();
    }




	public function model()
	{
        return PostImage::class;
    }

    public function byTags($tags)
    {

        return Cost::with(["tags", "comments"])->whereHas('tags', function ($query) use ($tags) {
                    $query->whereIn('name', explode(",", $tags));
                 })->where("organizer_id", Context::level()->get_organizer_id())->paginate(50);

    }

    public function mostUsedTags()
    {

        return App::make($this->model())->mostUsedTags();

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

    	$cost = new PostImage;

    	$cost->organizer_id = $this->context->get_organizer_id();

        $cost->fill($data)->save();

        return $cost;

    }

	public function update(array $data, $id)
	{
    	//return true;

    	$model = $this->find($id);

    	$model->organizer_id = $this->context->get_organizer_id();

        return $model->fill($data)->save();
    
    }


}