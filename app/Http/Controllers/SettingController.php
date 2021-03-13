<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = [
            'commission' => Setting::key('commission')
        ];

        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->isAdmin()) throw new UnauthorizedException();

        $request->validate([
            'commission' => 'required|numeric|min:0.001',
        ]);

        $commission = Setting::where('name', 'commission')->first();
        $commission->value= $request->commission;
        $commission->save();

        alert()->success('Settings updated successfully')->persistent(true, false);

        return redirect()->back();
    }
}
