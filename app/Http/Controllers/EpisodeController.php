<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\Episode;
use App\Models\Podcast;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($podcast_id)
    {
        Podcast::findOrFail($podcast_id);
        $episodes = Episode::where('podcast_id', $podcast_id)->get();
        return response()->json($episodes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEpisodeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $episode = Episode::findOrFail($id);

        return response()->json($episode, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEpisodeRequest $request, Episode $episode)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Episode $episode)
    {
        //
    }
}
