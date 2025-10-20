<?php

namespace App\Http\Controllers;

use Boctulus\FriendlyposWeb\Models\__MODEL_NAME__;
use Boctulus\FriendlyposWeb\Http\Resources\__RESOURCE_NAME__;
use Illuminate\Http\Request;

class __CONTROLLER_NAME__ extends Controller 
{
    protected $model = __MODEL_NAME__::class;
    protected $resource = __RESOURCE_NAME__::class;
    
    // __VALIDATION_RULES__

    public function index()
    {
        return $this->resource::collection($this->model::paginate());
    }

    public function store(Request $request)
    {
        $validated = $request->validate(static::$store_rules);
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
        $validated = $request->validate(static::$update_rules);
        $model->update($validated);
        return new $this->resource($model);
    }

    public function destroy($id)
    {
        $this->model::findOrFail($id)->delete();
        return response()->noContent();
    }
}