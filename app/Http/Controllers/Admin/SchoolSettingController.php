<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    public function edit()
    {
        $settings = SchoolSetting::firstOrCreate(
            ['id' => 1],
            [
                'school_name' => 'My School',
                'default_language' => 'en',
            ]
        );

        return view('admin.school-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = SchoolSetting::firstOrCreate(
            ['id' => 1],
            [
                'school_name' => 'My School',
                'default_language' => 'en',
            ]
        );

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'academic_year' => ['nullable', 'string', 'max:50'],
            'default_language' => ['required', 'in:en,ar'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $validated['logo_path'] = $request->file('logo')
                ->store('school-logos', 'public');
        }

        unset($validated['logo']);

        $settings->update($validated);

        return redirect()
            ->route('admin.school-settings.edit')
            ->with('success', 'School settings updated successfully.');
    }
}
