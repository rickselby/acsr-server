<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointsSequence;
use App\Services\PointsSequenceService;

use App\Http\Requests;

class PointsSequenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:points-admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('points-sequence.index')
            ->with('sequences', PointsSequence::orderBy('name')->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('points-sequence.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\PointsSequenceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\PointsSequenceRequest $request, PointsSequenceService $service)
    {
        $sequence = PointsSequence::create($request->all());

        // Also save points...
        $service->setPoints($sequence, $request->get('points'));

        \Notification::add('success', 'Points Sequence "'.$sequence->name.'" created');
        return \Redirect::route('admin.points-sequence.show', $sequence);
    }

    /**
     * Display the specified resource.
     *
     * @param  PointsSequence $points_sequence
     * @return \Illuminate\Http\Response
     */
    public function show(PointsSequence $points_sequence)
    {
        return view('points-sequence.show')
            ->with('sequence', $points_sequence);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PointsSequence $points_sequence
     * @return \Illuminate\Http\Response
     */
    public function edit(PointsSequence $points_sequence)
    {
        return view('points-sequence.edit')
            ->with('sequence', $points_sequence);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\PointsSequenceRequest  $request
     * @param  PointsSequence $points_sequence
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\PointsSequenceRequest $request, PointsSequence $points_sequence, PointsSequenceService $service)
    {
        $points_sequence->fill($request->all());
        $points_sequence->save();

        // Also save points...
        $service->setPoints($points_sequence, $request->get('points'));

        \Notification::add('success', 'Points Sequence "'.$points_sequence->name.'" updated');
        return \Redirect::route('admin.points-sequence.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PointsSequence $points_sequence
     * @return \Illuminate\Http\Response
     */
    public function destroy(PointsSequence $points_sequence, PointsSequenceService $service)
    {
        if ($service->isUsed($points_sequence)) {
            \Notification::add('error', 'Points Sequence "'.$points_sequence->name.'" cannot be deleted - it is in use');
        } else {
            $points_sequence->delete();
            \Notification::add('success', 'Points Sequence deleted');
        }
        return \Redirect::route('admin.points-sequence.index');
    }

}
