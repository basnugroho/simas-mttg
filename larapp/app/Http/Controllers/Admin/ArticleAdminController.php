<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Mosque;
use Illuminate\Support\Facades\Storage;

class ArticleAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Article::with('category','creator','mosque');
        if ($request->filled('status')) $q->where('status', $request->input('status'));
        $items = $q->orderBy('created_at','desc')->paginate(20)->appends($request->query());
        return view('admin.articles.index', ['items' => $items]);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $mosques = Mosque::orderBy('name')->get();
        return view('admin.articles.create', ['categories' => $categories, 'mosques' => $mosques]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:articles,slug',
            'category_id' => 'nullable|exists:categories,id',
            'mosque_id' => 'nullable|exists:mosques,id',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'action' => 'nullable|string',
        ]);

        $a = new Article();
        $a->title = $data['title'];
        $a->slug = $data['slug'] ?? 
            \Str::slug($data['title']) . '-' . time();
        $a->category_id = $data['category_id'] ?? null;
        $a->mosque_id = $data['mosque_id'] ?? null;
        $a->summary = $data['summary'] ?? null;
        $a->content = $data['content'] ?? null;
        // handle uploaded image file (preview/main image)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $a->image_url = Storage::url($path);
        } else {
            $a->image_url = null;
        }
        $a->created_by = auth()->id();

        if (($data['action'] ?? '') === 'publish') {
            $a->status = 'PUBLISHED';
            $a->published_at = now();
        } else {
            $a->status = 'DRAFT';
        }

        $a->save();
        return redirect()->route('admin.articles.index')->with('success','Article saved');
    }

    public function edit($id)
    {
        $a = Article::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $mosques = Mosque::orderBy('name')->get();
        return view('admin.articles.edit', ['article' => $a, 'categories' => $categories, 'mosques' => $mosques]);
    }

    public function update(Request $request, $id)
    {
        $a = Article::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:articles,slug,' . $a->id,
            'category_id' => 'nullable|exists:categories,id',
            'mosque_id' => 'nullable|exists:mosques,id',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'action' => 'nullable|string',
        ]);

        $a->title = $data['title'];
        $a->slug = $data['slug'] ?? $a->slug;
        $a->category_id = $data['category_id'] ?? null;
        $a->mosque_id = $data['mosque_id'] ?? null;
        $a->summary = $data['summary'] ?? null;
        $a->content = $data['content'] ?? null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $a->image_url = Storage::url($path);
        }
        if (($data['action'] ?? '') === 'publish') {
            $a->status = 'PUBLISHED';
            if (!$a->published_at) $a->published_at = now();
        }
        $a->save();
        return redirect()->route('admin.articles.index')->with('success','Article updated');
    }

    public function destroy($id)
    {
        $a = Article::findOrFail($id);
        $a->delete();
        return redirect()->route('admin.articles.index')->with('success','Article deleted');
    }

    /**
     * Upload image used by Trix or preview uploader.
     * Returns JSON { url: '...'} on success.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([ 'image' => 'required|image|max:8192' ]);
        $path = $request->file('image')->store('articles', 'public');
        $url = Storage::url($path);
        return response()->json(['url' => $url]);
    }

    /**
     * Admin preview for an article (renders regardless of publish status).
     */
    public function preview($id)
    {
        $a = Article::with('creator','mosque')->findOrFail($id);
        return view('admin.articles.preview', ['article' => $a]);
    }

    /**
     * Publish an article immediately.
     */
    public function publish(Request $request, $id)
    {
        $a = Article::findOrFail($id);
        $a->status = 'PUBLISHED';
        if (!$a->published_at) $a->published_at = now();
        $a->save();
        return redirect()->back()->with('success', 'Article published');
    }
}
