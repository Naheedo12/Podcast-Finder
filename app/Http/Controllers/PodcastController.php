<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePodcastRequest;
use App\Http\Requests\UpdatePodcastRequest;
use App\Models\Podcast;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Podcast::class);

        $podcasts = Podcast::with('user')->get();

        return response()->json($podcasts);
    }

    public function show($id)
    {
        $podcast = Podcast::with(['user', 'episodes'])->findOrFail($id);

        $this->authorize('view', $podcast);

        return response()->json($podcast, 200);
    }

    public function store(StorePodcastRequest $request)
    {
        $this->authorize('create', Podcast::class);

        $infos = $request->validated();

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->getRealPath();
            $uploadedImage = Cloudinary::upload($filePath)->getSecurePath();
            $infos['image'] = $uploadedImage;
        }

        $podcast = $request->user()->podcasts()->create($infos);

        return response()->json([
            'message' => 'Podcast créé avec succès',
            'podcast' => $podcast,
        ], 201);
    }

    public function update(UpdatePodcastRequest $request, Podcast $podcast)
    {
        $this->authorize('update', $podcast);

        $infos = $request->validated();

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->getRealPath();
            $uploadedImage = Cloudinary::upload($filePath)->getSecurePath();
            $infos['image'] = $uploadedImage;
        }

        $podcast->update($infos);

        return response()->json([
            'message' => 'Podcast modifié avec succès',
            'podcast' => $podcast,
        ], 200);
    }

    public function destroy(Podcast $podcast)
    {
        $this->authorize('delete', $podcast);

        $podcast->delete();

        return response()->json([
            'message' => 'Podcast supprimé avec succès',
        ], 200);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Podcast::class);

        $query = Podcast::query();

        if ($request->has('titre')) {
            $query->where('titre', 'like', '%'.$request->titre.'%');
        }

        if ($request->has('categorie')) {
            $query->where('categorie', 'like', '%'.$request->categorie.'%');
        }

        $podcasts = $query->get();

        return response()->json($podcasts, 200);
    }
}
