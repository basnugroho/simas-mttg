<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Mosque;
use App\Models\Regions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MessageController extends Controller
{
    // list messages with filters, scoped by user's regions
    public function index(Request $request)
    {
        $user = auth()->user();

        // build expanded region ids like dashboard did
        $allExpanded = [];
        try {
            foreach ($user->regionsRoles()->get() as $ar) {
                $rid = (int)$ar->region_id;
                try { $desc = Regions::collectDescendantIds($rid); }
                catch (\Throwable $e) { $desc = [$rid]; }
                $expanded = is_array($desc) ? $desc : (is_callable([$desc, 'toArray']) ? $desc->toArray() : [$rid]);
                $allExpanded = array_merge($allExpanded, $expanded);
            }
            $allExpanded = array_values(array_unique($allExpanded));
        } catch (\Throwable $e) { $allExpanded = []; }

        // find mosque ids under these regions; if empty, show all
        $mosqueIds = [];
        if (count($allExpanded)) {
            $q = Mosque::query();
            $q->whereIn('regional_id', $allExpanded)
              ->orWhereIn('area_id', $allExpanded)
              ->orWhereIn('witel_id', $allExpanded)
              ->orWhereIn('sto_id', $allExpanded);
            $mosqueIds = $q->pluck('id')->toArray();
        }

        $q = Message::query();
        if (count($mosqueIds)) $q->whereIn('mosque_id', $mosqueIds);

        if ($request->filled('mosque_id')) $q->where('mosque_id', $request->input('mosque_id'));
        if ($request->filled('search')) {
            $s = $request->input('search');
            $q->where(function($qq) use ($s) {
                $qq->where('subject','like','%'.$s.'%')
                   ->orWhere('message','like','%'.$s.'%')
                   ->orWhere('name','like','%'.$s.'%')
                   ->orWhere('email','like','%'.$s.'%');
            });
        }
        if ($request->filled('from')) $q->whereDate('created_at','>=',$request->input('from'));
        if ($request->filled('to')) $q->whereDate('created_at','<=',$request->input('to'));

        $items = $q->orderBy('created_at','desc')->paginate(25)->appends($request->query());

        // list mosques for filter
        $mosques = Mosque::whereIn('id', $items->pluck('mosque_id')->filter()->unique()->toArray())->get();

        return view('admin.messages.index', ['items' => $items, 'mosques' => $mosques]);
    }

    // show single message and mark read
    public function show($id)
    {
        $m = Message::findOrFail($id);
        // Only try to mark read if the column exists (migration may be pending)
        if (Schema::hasColumn('messages', 'is_read')) {
            if (!$m->is_read) {
                $m->is_read = true;
                $m->read_at = now();
                $m->save();
            }
        }
        return view('admin.messages.show', ['m' => $m]);
    }

    /**
     * Open message via AJAX: mark read and return rendered panel + unread count
     */
    public function open(Request $request, $id)
    {
        $m = Message::findOrFail($id);
        // mark as read only if the DB column exists
        if (Schema::hasColumn('messages', 'is_read')) {
            if (!$m->is_read) {
                $m->is_read = true;
                $m->read_at = now();
                $m->save();
            }
        }

        // recompute unread count for current user scope (similar to index)
        $user = auth()->user();
        $allExpanded = [];
        try {
            foreach ($user->regionsRoles()->get() as $ar) {
                $rid = (int)$ar->region_id;
                try { $desc = Regions::collectDescendantIds($rid); }
                catch (\Throwable $e) { $desc = [$rid]; }
                $expanded = is_array($desc) ? $desc : (is_callable([$desc, 'toArray']) ? $desc->toArray() : [$rid]);
                $allExpanded = array_merge($allExpanded, $expanded);
            }
            $allExpanded = array_values(array_unique($allExpanded));
        } catch (\Throwable $e) { $allExpanded = []; }

        $unread = 0;
        try {
            if (!Schema::hasColumn('messages', 'is_read')) {
                $unread = 0;
            } else {
                if (!empty($allExpanded)) {
                    $mq = Mosque::query();
                    $mq->whereIn('regional_id', $allExpanded)
                       ->orWhereIn('area_id', $allExpanded)
                       ->orWhereIn('witel_id', $allExpanded)
                       ->orWhereIn('sto_id', $allExpanded);
                    $mosqueIds = $mq->pluck('id')->toArray();
                    if (count($mosqueIds)) {
                        $unread = Message::whereIn('mosque_id', $mosqueIds)->where('is_read', false)->count();
                    }
                } else {
                    $unread = Message::where('is_read', false)->count();
                }
            }
        } catch (\Throwable $e) { $unread = 0; }

        $html = view('admin.messages._panel', ['m' => $m])->render();
        return response()->json(['html' => $html, 'unread' => $unread]);
    }

    /**
     * Mark multiple messages as unread (bulk action).
     * Expects JSON body: { ids: [1,2,3] }
     */
    public function markUnread(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) $ids = [];
        if (count($ids) && Schema::hasColumn('messages', 'is_read')) {
            Message::whereIn('id', $ids)->update(['is_read' => false, 'read_at' => null]);
        }

        // recompute unread count for current user scope (same as open/index)
        $user = auth()->user();
        $allExpanded = [];
        try {
            foreach ($user->regionsRoles()->get() as $ar) {
                $rid = (int)$ar->region_id;
                try { $desc = Regions::collectDescendantIds($rid); }
                catch (\Throwable $e) { $desc = [$rid]; }
                $expanded = is_array($desc) ? $desc : (is_callable([$desc, 'toArray']) ? $desc->toArray() : [$rid]);
                $allExpanded = array_merge($allExpanded, $expanded);
            }
            $allExpanded = array_values(array_unique($allExpanded));
        } catch (\Throwable $e) { $allExpanded = []; }

        $unread = 0;
        try {
            if (!Schema::hasColumn('messages', 'is_read')) {
                $unread = 0;
            } else {
                if (!empty($allExpanded)) {
                    $mq = Mosque::query();
                    $mq->whereIn('regional_id', $allExpanded)
                       ->orWhereIn('area_id', $allExpanded)
                       ->orWhereIn('witel_id', $allExpanded)
                       ->orWhereIn('sto_id', $allExpanded);
                    $mosqueIds = $mq->pluck('id')->toArray();
                    if (count($mosqueIds)) {
                        $unread = Message::whereIn('mosque_id', $mosqueIds)->where('is_read', false)->count();
                    }
                } else {
                    $unread = Message::where('is_read', false)->count();
                }
            }
        } catch (\Throwable $e) { $unread = 0; }

        return response()->json(['status' => 'ok', 'unread' => $unread]);
    }

    /**
     * Return unread count for current user's scope (used by sidebar AJAX polling).
     */
    public function unreadCount(Request $request)
    {
        $user = auth()->user();
        $allExpanded = [];
        try {
            foreach ($user->regionsRoles()->get() as $ar) {
                $rid = (int)$ar->region_id;
                try { $desc = Regions::collectDescendantIds($rid); }
                catch (\Throwable $e) { $desc = [$rid]; }
                $expanded = is_array($desc) ? $desc : (is_callable([$desc, 'toArray']) ? $desc->toArray() : [$rid]);
                $allExpanded = array_merge($allExpanded, $expanded);
            }
            $allExpanded = array_values(array_unique($allExpanded));
        } catch (\Throwable $e) { $allExpanded = []; }

        $unread = 0;
        try {
            if (!Schema::hasColumn('messages', 'is_read')) {
                $unread = 0;
            } else {
                if (!empty($allExpanded)) {
                    $mq = Mosque::query();
                    $mq->whereIn('regional_id', $allExpanded)
                       ->orWhereIn('area_id', $allExpanded)
                       ->orWhereIn('witel_id', $allExpanded)
                       ->orWhereIn('sto_id', $allExpanded);
                    $mosqueIds = $mq->pluck('id')->toArray();
                    if (count($mosqueIds)) {
                        $unread = Message::whereIn('mosque_id', $mosqueIds)->where('is_read', false)->count();
                    }
                } else {
                    $unread = Message::where('is_read', false)->count();
                }
            }
        } catch (\Throwable $e) { $unread = 0; }

        return response()->json(['unread' => $unread]);
    }

    // delete message
    public function destroy($id)
    {
        $m = Message::findOrFail($id);
        $m->delete();
        return redirect()->route('admin.messages.index')->with('success','Message deleted');
    }
}
