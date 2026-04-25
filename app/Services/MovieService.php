<?php

namespace App\Services;

use App\Models\Movie;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MovieService
{
    public function getAllMovies($search = null)
    {
        $query = Movie::latest();

        if ($search) {
            $query->where('judul', 'like', '%' . $search . '%')
                ->orWhere('sinopsis', 'like', '%' . $search . '%');
        }

        return $query->paginate(6)->appends(['search' => $search]);
    }

    public function getMovieById($id)
    {
        return Movie::findOrFail($id);
    }

    public function createMovie($request)
    {
        $randomName = Str::uuid()->toString();
        $fileName = $randomName . '.jpg';

        $request->file('foto_sampul')->move(public_path('images'), $fileName);

        return Movie::create([
            'id' => $request->id,
            'judul' => $request->judul,
            'category_id' => $request->category_id,
            'sinopsis' => $request->sinopsis,
            'tahun' => $request->tahun,
            'pemain' => $request->pemain,
            'foto_sampul' => $fileName,
        ]);
    }

    public function updateMovie($request, $id)
    {
        $movie = Movie::findOrFail($id);

        if ($request->hasFile('foto_sampul')) {
            $randomName = Str::uuid()->toString();
            $fileName = $randomName . '.' . $request->file('foto_sampul')->getClientOriginalExtension();

            $request->file('foto_sampul')->move(public_path('images'), $fileName);

            if (File::exists(public_path('images/' . $movie->foto_sampul))) {
                File::delete(public_path('images/' . $movie->foto_sampul));
            }

            $movie->update([
                'judul' => $request->judul,
                'sinopsis' => $request->sinopsis,
                'category_id' => $request->category_id,
                'tahun' => $request->tahun,
                'pemain' => $request->pemain,
                'foto_sampul' => $fileName,
            ]);
        } else {
            $movie->update([
                'judul' => $request->judul,
                'sinopsis' => $request->sinopsis,
                'category_id' => $request->category_id,
                'tahun' => $request->tahun,
                'pemain' => $request->pemain,
            ]);
        }

        return $movie;
    }

    public function deleteMovie($id)
    {
        $movie = Movie::findOrFail($id);

        if (File::exists(public_path('images/' . $movie->foto_sampul))) {
            File::delete(public_path('images/' . $movie->foto_sampul));
        }

        return $movie->delete();
    }
}