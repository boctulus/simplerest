<?php

namespace App\Http\Controllers;

//uses
use App\Models\Favorite;
use App\Http\Resources\FavoriteResource;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
   //traits
   protected $model = Favorite::class;
   protected $resource = FavoriteResource::class;

   // __VALIDATION_RULES__

   public function index()
   {
       return $this->resource::collection($this->model::paginate());
   }

   public function store(Request $request) 
   {
       $validated = $request->validate($this->store_rules);
       $model = $this->model::create($validated);
       return new $this->resource($model);
   }

   public function show($id)
   {
       $model = $this->model::findOrFail($id);
       return new $this->resource($model);
   }

   public function update(Request $request, $id)
   {
       $model = $this->model::findOrFail($id);
       $validated = $request->validate($this->update_rules);
       $model->update($validated);
       return new $this->resource($model);
   }

   public function destroy($id)
   {
       $this->model::findOrFail($id)->delete();
       return response()->noContent();
   }
}