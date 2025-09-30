<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = Card::with('user')->get();
        return response()->json([
            'cards' => $cards
        ]);
    }

    public function belongToUser()
    {
        $cards = Card::with('user')->where('user_id', auth()->id())->get();
        return response()->json([
            'cards' => $cards
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:99999',
            'image' => 'nullable|mimes:png,jpg,jpeg,gif,webp|max:2048'
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('cards', 'public');
        }

        $card = Card::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $validated['image'] ?? null,
            'user_id' => auth()->id()
        ]);
        return response()->json([
            'card' => $card
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        return response()->json([
            'card' => $card->load('user')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Card $card)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Card $card)
    {
        //check ownership
        if ($card->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'unauthorized'
            ], 403);
        }
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:99999',
            'image' => 'sometimes|mimes:jpg,png,jpeg,gif,webp|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($card->image) {
                Storage::disk('public')->delete($card->image);
            }
            $validated['image'] = $request->file('image')->store('cards', 'public');
        }

        $card->update($validated);
        return response()->json([
            'card' => $card,
            'message' => "update successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        if ($card->usre_id !== auth()->id()) {
            return response()->json([
                'message' => 'unauthorized'
            ], 403);
        }

        if ($card->image) {
            Storage::disk('public')->delete($card->image);
        }
        return response()->json([
            'message' => 'card deleted successfully'
        ]);
    }
}
