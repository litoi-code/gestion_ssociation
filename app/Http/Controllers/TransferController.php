<?php

namespace App\Http\Controllers;



use App\Models\Transfer;

use App\Models\Member;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transfers = Transfer::with('sender', 'receiver')->get();
        return view('transfers.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::all();
        return view('transfers.create', compact('members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:members,id',
            'receiver_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Transfer::create($request->all());

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transfer $transfer)
    {
        $transfer = Transfer::with('sender', 'receiver')->findOrFail($transfer->id);
        return view('transfers.show', compact('transfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transfer $transfer)
    {
        $members = Member::all();
        return view('transfers.edit', compact('transfer', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\Illuminate\Http\Request $request, Transfer $transfer)
    {
        $request->validate([
            'sender_id' => 'required|exists:members,id',
            'receiver_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $transfer->update($request->all());

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transfer $transfer)
    {
        $transfer->delete();

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer deleted successfully.');
    }
}
