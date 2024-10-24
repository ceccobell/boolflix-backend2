<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function getUserFavorites()
    {
        $userId = Auth::id();
        $favorites = Favorite::where('user_id', $userId)->get();

        return response()->json($favorites);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'type' => 'required|in:movie,tv',
        ]);

        $favorite = Favorite::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
            'type' => $request->type,
        ]);

        return response()->json(['message' => 'Aggiunto ai preferiti con successo!', 'favorite' => $favorite], 201);
    }


    // Rimuovi un elemento dai preferiti
    public function destroy($id)
    {
        $favorite = Favorite::where('user_id', Auth::id())->where('id', $id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Rimosso dai preferiti con successo!']);
        } else {
            return response()->json(['error' => 'Elemento non trovato'], 404);
        }
    }
}

