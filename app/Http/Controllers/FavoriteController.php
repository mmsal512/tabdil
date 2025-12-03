<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()->latest()->get();
        return view('dashboard', compact('favorites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'base_currency' => 'required|string|size:3',
            'target_currency' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0',
            'converted_amount' => 'nullable|numeric|min:0',
            'label' => 'nullable|string|max:255',
        ]);

        Auth::user()->favorites()->create($request->all());

        return redirect()->back()->with('success', __('Conversion saved to favorites!'));
    }

    public function destroy(Favorite $favorite)
    {
        if ($favorite->user_id !== Auth::id()) {
            abort(403);
        }

        $favorite->delete();

        return redirect()->back()->with('success', __('Favorite removed.'));
    }

    public function destroyMany(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:favorites,id',
        ]);

        Auth::user()->favorites()->whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', __('Favorite removed.'));
    }
}
