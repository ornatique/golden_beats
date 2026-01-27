<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index()
    {
        return view('admin.events.index');
    }

    public function data()
    {
        $events = Event::latest();

        return DataTables::of($events)
            ->addIndexColumn()

            ->addColumn('image', function ($e) {

                if (empty($e->image)) {
                    return '-';
                }

                // If image is JSON / array → take first image
                $image = is_array($e->image)
                    ? $e->image[0]
                    : $e->image;

                return '<img src="' . asset('uploads/events/' . $image) . '"
                width="60"
                style="border-radius:4px">';
            })


            ->editColumn('event_date', function ($e) {
                return Carbon::parse($e->event_date)->format('d M Y h:i A');
            })
            ->addColumn('event_type', function ($e) {
                $colors = [
                    'upcoming' => 'warning',
                    'live' => 'success',
                    'completed' => 'secondary'
                ];

                return '<span class="badge badge-' . $colors[$e->event_type] . '">'
                    . ucfirst($e->event_type) . '</span>';
            })
            ->addColumn('action', function ($e) {

                $buttons = '';

                // ✅ EDIT (optional permission)
                if (auth()->user()->can('event-edit')) {
                    $buttons .= '
            <a href="' . route('admin.events.edit', $e->id) . '"
               class="btn btn-sm btn-primary mr-1">
                Edit
            </a>
        ';
                }

                // ✅ DELETE (permission check)
                if (auth()->user()->can('event-delete')) {
                    $buttons .= '
            <form action="' . route('admin.events.destroy', $e->id) . '"
                  method="POST"
                  style="display:inline-block"
                  onsubmit="return confirm(\'Delete this event?\')">
                ' . csrf_field() . method_field('DELETE') . '
                <button class="btn btn-sm btn-danger">
                    Delete
                </button>
            </form>
        ';
                }

                return $buttons ?: '-';
            })
            ->rawColumns(['action'])

            ->rawColumns(['action'])


            ->rawColumns(['image', 'action', 'event_type'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        // 1️⃣ Validate
        $data = $request->validate([
            'title'        => 'required|min:3',
            'event_date'   => 'required|date',
            'location'     => 'required',
            'event_type'   => 'required',
            'map_link'     => 'required|url',
            'description'  => 'required',
            'image'       => 'required|array',
            'image.*'     => 'image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        // 2️⃣ Upload Images
        $images = [];

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $image) {
                $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/events'), $name);
                $images[] = $name;
            }
        }

        // 3️⃣ Save images as JSON
        $data['image'] = json_encode($images);

        // 4️⃣ Create Event
        Event::create($data);

        // 5️⃣ Redirect
        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title'       => 'required|string|min:3',
            'event_date'  => 'required|date',
            'location'    => 'required|string',
            'event_type'  => 'required|string',
            'map_link'    => 'nullable|url',
            'description' => 'nullable|string',
            'image.*'     => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
            'removed_images' => 'nullable|string',
        ]);

        /* -----------------------------
         | EXISTING IMAGES
         ----------------------------- */
        $existingImages = $event->image ?? [];
        $removedImages  = $request->removed_images
            ? json_decode($request->removed_images, true)
            : [];

        /* DELETE REMOVED IMAGES */
        foreach ($removedImages as $img) {
            $path = public_path('uploads/events/' . $img);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        /* KEEP REMAINING IMAGES */
        $finalImages = array_values(array_diff($existingImages, $removedImages));

        /* ADD NEW IMAGES */
        if ($request->hasFile('image')) {
            $path = public_path('uploads/events');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            foreach ($request->file('image') as $img) {
                $name = time() . '_' . uniqid() . '.' . $img->extension();
                $img->move($path, $name);
                $finalImages[] = $name;
            }
        }

        $event->update([
            'title'       => $data['title'],
            'event_date'  => $data['event_date'],
            'location'    => $data['location'],
            'event_type'  => $data['event_type'],
            'map_link'    => $data['map_link'] ?? null,
            'description' => $data['description'] ?? null,
            'image'      => $finalImages,
        ]);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully');
    }

    public function destroy(Event $event)
    {
        // delete image if exists
        if ($event->image && File::exists(public_path($event->image))) {
            File::delete(public_path($event->image));
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully');
    }
}
