<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaLink;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function index()
    {
        // Always get first row or create empty
        $social = SocialMediaLink::firstOrCreate([]);
        return view('admin.social-media.index', compact('social'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'facebook'  => 'nullable|url',
            'twitter'   => 'nullable|url',
            'instagram' => 'nullable|url',
            'linkedin'  => 'nullable|url',
            'whatsapp'  => 'nullable|string',
            'youtube'   => 'nullable|url',
        ]);

        SocialMediaLink::first()->update($data);

        return back()->with('success', 'Social media links updated');
    }
}
