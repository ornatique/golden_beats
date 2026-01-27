<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reel;
use App\Models\ReelLike;
use App\Models\ReelComment;
use App\Models\Category;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class ReelController extends Controller
{
    public function index()
    {
        return view('admin.reels.index');
    }

    public function data()
    {
        $reels = Reel::query()
            ->with(['category', 'subcategory'])
            ->withCount([
                'likes as likes_total',
                'comments as comments_total'
            ])
            ->latest();

        return DataTables::of($reels)
            ->addIndexColumn()

            ->addColumn('media', function ($r) {

                $html = '<div class="reel-media-wrap">';

                // Thumbnail image
                if (!empty($r->image) && file_exists(public_path($r->image))) {
                    $html .= '
            <img src="' . asset($r->image) . '"
                 class="reel-thumb"
                 alt="Thumbnail">
        ';
                }

                // Video preview
                if (!empty($r->media_file) && file_exists(public_path($r->media_file))) {
                    $html .= '
            <video class="reel-video" >
                <source src="' . asset($r->media_file) . '" type="video/mp4">
            </video>
        ';
                }

                $html .= '</div>';

                return $html;
            })

            ->addColumn('category', fn($r) => $r->category->name ?? '-')
            ->addColumn('subcategory', fn($r) => $r->subcategory->name ?? '-')

            ->addColumn('stats', function ($r) {
                return '
                <span class="badge bg-success">❤ ' . $r->likes_total . '</span>
                <span class="badge bg-info">💬 ' . $r->comments_total . '</span>
            ';
            })

            ->addColumn('action', function ($r) {

                $html = '';

                // 💬 VIEW COMMENTS (reel-view)
                if (auth()->user()->can('view-comment')) {
                    $html .= '
            <button class="btn btn-info btn-sm mr-1"
                onclick="openComments(' . $r->id . ')">
                💬 Comments (' . $r->comments_total . ')
            </button>
        ';
                }

                // ✏️ EDIT REEL (reel-edit)
                if (auth()->user()->can('reel-edit')) {
                    $html .= '
            <a href="' . route('admin.reels.edit', $r->id) . '"
               class="btn btn-primary btn-sm mr-1">
                Edit
            </a>
        ';
                }

                // 🗑 DELETE REEL (reel-delete)
                if (auth()->user()->can('reel-delete')) {
                    $html .= '
            <form action="' . route('admin.reels.destroy', $r->id) . '"
                  method="POST"
                  style="display:inline-block"
                  onsubmit="return confirm(\'Delete reel?\')">
                ' . csrf_field() . method_field('DELETE') . '
                <button class="btn btn-danger btn-sm">
                    Delete
                </button>
            </form>
        ';
                }

                return $html ?: '-';
            })
            ->rawColumns(['action'])


            ->rawColumns(['media', 'stats', 'action'])
            ->make(true);
    }


    public function create()
    {
        return view('admin.reels.create', [
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required',
            'description'   => 'nullable',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'media_file'    => 'required|mimes:mp4,mov,avi|max:51200', // 50MB
            'category_id'   => 'nullable',
            'subcategory_id' => 'nullable'
        ]);

        /* ===============================
       IMAGE UPLOAD (PUBLIC FOLDER)
    =============================== */
        if ($request->hasFile('image')) {

            $image      = $request->file('image');
            $imageName  = time() . '_img.' . $image->getClientOriginalExtension();
            $imagePath  = public_path('uploads/reels');

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $image->move($imagePath, $imageName);

            // Save relative path in DB
            $data['image'] = 'uploads/reels/' . $imageName;
        }

        /* ===============================
       VIDEO UPLOAD (PUBLIC FOLDER)
    =============================== */
        if ($request->hasFile('media_file')) {

            $video      = $request->file('media_file');
            $videoName  = time() . '_video.' . $video->getClientOriginalExtension();
            $videoPath  = public_path('uploads/reels');

            if (!file_exists($videoPath)) {
                mkdir($videoPath, 0755, true);
            }

            $video->move($videoPath, $videoName);

            // Save relative path in DB
            $data['media_file'] = 'uploads/reels/' . $videoName;
        }
        $data['story'] = $request->story;
        Reel::create($data);

        return redirect()
            ->route('admin.reels.index')
            ->with('success', 'Reel created successfully');
    }


    public function edit(Reel $reel)
    {
        return view('admin.reels.edit', [
            'reel' => $reel,
            'categories' => Category::all()
        ]);
    }

    public function update(Request $request, Reel $reel)
    {
        $data = $request->validate([
            'name'           => 'required',
            'description'    => 'nullable',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'media_file'     => 'nullable|mimes:mp4,mov,avi|max:51200', // 50MB
            'category_id'    => 'nullable',
            'subcategory_id' => 'nullable'
        ]);

        /* ===============================
       IMAGE UPDATE (PUBLIC FOLDER)
    =============================== */
        if ($request->hasFile('image')) {

            // 🔥 Delete old image
            if ($reel->image && file_exists(public_path($reel->image))) {
                unlink(public_path($reel->image));
            }

            $image     = $request->file('image');
            $imageName = time() . '_img.' . $image->getClientOriginalExtension();
            $imagePath = public_path('uploads/reels');

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $image->move($imagePath, $imageName);
            $data['image'] = 'uploads/reels/' . $imageName;
        }

        /* ===============================
       VIDEO UPDATE (PUBLIC FOLDER)
    =============================== */
        if ($request->hasFile('media_file')) {

            // 🔥 Delete old video
            if ($reel->media_file && file_exists(public_path($reel->media_file))) {
                unlink(public_path($reel->media_file));
            }

            $video     = $request->file('media_file');
            $videoName = time() . '_video.' . $video->getClientOriginalExtension();
            $videoPath = public_path('uploads/reels');

            if (!file_exists($videoPath)) {
                mkdir($videoPath, 0755, true);
            }

            $video->move($videoPath, $videoName);
            $data['media_file'] = 'uploads/reels/' . $videoName;
        }
        $data['story'] = $request->story;
        $reel->update($data);

        return redirect()
            ->route('admin.reels.index')
            ->with('success', 'Reel updated successfully');
    }


    public function destroy(Reel $reel)
    {
        $reel->delete();
        return back()->with('success', 'Reel deleted');
    }

    public function comments(Reel $reel)
    {
        $comments = $reel->comments()->with('user')->latest()->get();

        return response()->json($comments);
    }

    public function updateComment(Request $request, ReelComment $comment)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $request->comment
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteComment(ReelComment $comment)
    {
        $comment->delete();

        return response()->json(['success' => true]);
    }
}
