<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{

    public function show($id)
    {
        $lead = Lead::with(['source','status','assignedTo','customer'])
                    ->findOrFail($id);

        return view('leads.show', compact('lead'));
    }


    public function edit($id)
    {
        $lead = Lead::findOrFail($id);

        $sources = LeadSource::active()->get();
        $statuses = LeadStatus::active()->get();
        $agents = User::where('role_id','!=',null)->get();

        return view('leads.edit', compact(
            'lead',
            'sources',
            'statuses',
            'agents'
        ));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required',
            'phone' => 'required'
        ]);

        $lead = Lead::findOrFail($id);

        $lead->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'lead_source_id' => $request->lead_source_id,
            'lead_status_id' => $request->lead_status_id,
            'assigned_to' => $request->assigned_to,
            'next_follow_up_date' => $request->next_follow_up_date,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('leads.show',$lead->id)
            ->with('success','Lead updated successfully');
    }
}