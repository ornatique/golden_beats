<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PopupBannerAd;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PopupBannerAdController extends Controller
{
    public function index()
    {
        return view('admin.popup_banner_ads.index');
    }

    public function data()
    {
        $ads = PopupBannerAd::latest();

        return DataTables::of($ads)
            ->addIndexColumn()

            ->addColumn('image', function ($ad) {
                return $ad->image
                    ? '<img src="' . asset($ad->image) . '" width="80">'
                    : '-';
            })

            ->addColumn('status', function ($ad) {

                $class = $ad->status === 'Active'
                    ? 'bg-success'
                    : 'bg-danger';

                return '<span class="badge ' . $class . '">' . $ad->status . '</span>';
            })


            ->addColumn('action', function ($ad) {
                return '
                    <a href="' . route('admin.popup-banner-ads.edit', $ad->id) . '"
                       class="btn btn-primary btn-sm">Edit</a>
                ';
            })

            ->rawColumns(['image', 'status', 'action'])
            ->make(true);
    }

    public function edit(PopupBannerAd $popupBannerAd)
    {
        // dd($popupBannerAd);
        return view('admin.popup_banner_ads.edit', compact('popupBannerAd'));
    }

    public function update(Request $request, PopupBannerAd $popupBannerAd)
    {
        $data = $request->validate([
            'title' => 'nullable|string',
            'status' => 'required',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/popup_banner_ads'), $filename);
            $data['image'] = 'uploads/popup_banner_ads/' . $filename;
        }

        $popupBannerAd->update($data);

        return redirect()
            ->route('admin.popup-banner-ads.index')
            ->with('success', 'Popup Banner updated');
    }
}
