<?php

namespace App\Place\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Place\Http\Requests\BuildingCreateRequest;
use App\Place\Http\Requests\BuildingDeleteRequest;
use App\Place\Http\Requests\BuildingUpdateRequest;
use App\Place\Http\Requests\BuildingViewRequest;
use App\Place\Http\Resources\BuildingResource;
use App\Place\Models\Building;

class BuildingController extends Controller
{
    /**
     * Get buildings.
     *
     * @param  \App\Http\Requests\BuildingViewRequest $request     *
     * @return \Illuminate\Http\Response
     */
    public function index(BuildingViewRequest $request)
    {
        return BuildingResource::collection(Building::paginate($request->per_page));
    }

    /**
     * Show a building.
     *
     * @param  \App\Http\Requests\BuildingViewRequest $request     *
     * @return \Illuminate\Http\Response
     */
    public function show(BuildingViewRequest $request, Building $Building)
    {
        return new BuildingResource($Building);
    }

    /**
     * Create a building.
     *
     * @param  \App\Http\Requests\BuildingCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(BuildingCreateRequest $request)
    {
        return new BuildingResource(Building::create($request->all()));
    }

    /**
     * Update a building.
     *
     * @param  \App\Http\Requests\BuildingUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(BuildingUpdateRequest $request, Building $building)
    {
        $building->update($request->all());

        return new BuildingResource($building->fresh());
    }

    /**
     * Delete a building.
     *
     * @param  \App\Http\Requests\BuildingDeleteRequest $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(BuildingDeleteRequest $request, Building $building)
    {
        $building->delete();

        return new BuildingResource($building);
    }
}