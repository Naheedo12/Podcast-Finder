<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\Episode;
use App\Models\Podcast;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EpisodeController extends Controller
{

    public function index($podcast_id)
    {
        $podcast = Podcast::findOrFail($podcast_id);
        $this->authorize('viewAny', Episode::class);

        $episodes = Episode::where('podcast_id', $podcast_id)->get();

        return response()->json($episodes, 200);
    }

    public function store(StoreEpisodeRequest $request)
    {
        $this->authorize('create', Episode::class);

        if ($request->user()->role !== 'administrateur') {
            $podcast = Podcast::findOrFail($request->podcast_id);
            if (! Gate::allows('own-podcast', $podcast)) {
                return response()->json(['message' => 'Accès refusé : vous n\'êtes pas propriétaire de ce podcast'], 403);
            }
        }

        $infos = $request->validated();

        if ($request->hasFile('audio')) {
            $filePath = $request->file('audio')->getRealPath();
            $uploadedAudio = Cloudinary::upload($filePath, [
                'resource_type' => 'video',
            ])->getSecurePath();
            $infos['audio'] = $uploadedAudio;
        }

        $episode = Episode::create($infos);

        return response()->json([
            'message' => 'Épisode créé avec succès',
            'episode' => $episode,
        ], 201);
    }

    public function show($id)
    {
        $episode = Episode::with('podcast.user')->findOrFail($id);
        $this->authorize('view', $episode);

        return response()->json($episode, 200);
    }

    public function update(UpdateEpisodeRequest $request, Episode $episode)
    {
        $this->authorize('update', $episode);

        $infos = $request->validated();

        if ($request->hasFile('audio')) {
            $filePath = $request->file('audio')->getRealPath();
            $uploadedAudio = Cloudinary::upload($filePath, [
                'resource_type' => 'video',
            ])->getSecurePath();
            $infos['audio'] = $uploadedAudio;
        }

        $episode->update($infos);

        return response()->json([
            'message' => 'Épisode modifié avec succès',
            'episode' => $episode,
        ], 200);
    }

    public function destroy(Episode $episode)
    {
        $this->authorize('delete', $episode);

        $episode->delete();

        return response()->json([
            'message' => 'Épisode supprimé avec succès',
        ], 200);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Episode::class);

        $query = Episode::query();

        if ($request->has('titre')) {
            $query->where('titre', 'like', '%'.$request->titre.'%');
        }

        $episodes = $query->get();

        return response()->json($episodes, 200);
    }
}
