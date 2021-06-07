<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Payroll\Models\Deduction;
use Payroll\Models\Payroll;

class CalculatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deduction = Deduction::with([
            'slabs' => function ($builder) {
                return $builder->select([
                    'deduction_id', 'slab_number', 'min_amount', 'max_amount', 'rate'
                ]);
            },
            'relief' => function ($builder) {
                return $builder->select([
                    "reliefable_id", "reliefable_type", "amount"
                ]);
            },
        ])
            ->where('id', '<', 4)
            ->get(['id', 'threshold', 'type'])
            ->keyBy('id')
            ->map(function ($ded) {
                $slabs = $ded->slabs;
                $relief = $ded->relief;
                unset($ded->id, $ded->slabs, $ded->relief);

                $ded->slabs = $slabs->keyBy('slab_number')->map(function ($slab) {
                    unset($slab->deduction_id);
                    unset($slab->slab_number);

                    return $slab;
                })
                    ->toArray();

                $ded->relief = $relief ? $relief->amount : 0;

                return $ded;
            });
//        dd($deduction);

        return view('modules.payroll.calculator.index')->with('deduction', $deduction);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
