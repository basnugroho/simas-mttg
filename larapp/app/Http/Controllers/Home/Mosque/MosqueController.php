<?php

namespace App\Http\Controllers\Home\Mosque;

use App\Http\Controllers\Controller;
use App\Models\Mosque;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MosqueController extends Controller
{
    public function index()
    {
        $query = Mosque::query()->where('is_active', true)->with(['city','province','witel','photos','regional','area','sto']);

        $provinceId = request()->query('province_id');
        $cityId = request()->query('city_id');
        $witelId = request()->query('witel_id');
        $stoId = request()->query('sto_id');
        $facilityId = request()->query('facility_id');
        $type = request()->query('type');
        $q = request()->query('q');
        $regionalId = request()->query('regional_id');
        $areaId = request()->query('province_id'); // area is carried via province_id param in current UI

        if ($regionalId) {
            $query->where('regional_id', $regionalId);
        }
        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($areaId) {
            $query->where('area_id', $areaId);
        }

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        if ($witelId) {
            $query->where('witel_id', $witelId);
        }

        if ($stoId) {
            $query->where('sto_id', $stoId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($facilityId) {
            $query->whereHas('facility', function ($sub) use ($facilityId) {
                $sub->where('facilities.id', $facilityId);
            });
        }

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $mosques = $query->orderBy('name')->paginate(12)->withQueryString();

        // For each mosque in the paginated result, prefer the first photo's `path` from
        // `mosque_photos` as the image source (use Storage::url to create public URL).
        $mosques->getCollection()->transform(function ($m) {
            // If there is a photo with a path, use it
            if ($m->relationLoaded('photos') && $m->photos->count()) {
                $first = $m->photos->first();
                if (!empty($first->path)) {
                    try {
                        $m->db_image_url = \Illuminate\Support\Facades\Storage::url($first->path);
                    } catch (\Exception $e) {
                        $m->db_image_url = null;
                    }
                    return $m;
                }
            }
            // Fallback: leave existing db_image_url (model accessor may compute from image_url)
            $m->db_image_url = $m->db_image_url ?? null;
            return $m;
        });

        // Options for filters
        $regionals = \App\Models\Regions::where('level', 'REGIONAL')->orderBy('name')->get();
        // Area list (named provinces in existing view)
        if ($regionalId) {
            $provinces = \App\Models\Regions::where('level', 'AREA')->where('parent_id', $regionalId)->orderBy('name')->get();
        } else {
            $provinces = \App\Models\Regions::where('level', 'AREA')->orderBy('name')->get();
        }

        // If a province is selected, scope cities and witels to that province's direct children
        if ($provinceId) {
            $witels = \App\Models\Regions::where('parent_id', $provinceId)->where('level', 'WITEL')->orderBy('name')->get();
        } else {
            // no area selected: if regional selected, witels are children of areas under that regional
            if ($regionalId) {
                $areaIds = \App\Models\Regions::where('level','AREA')->where('parent_id',$regionalId)->pluck('id');
                $witels = \App\Models\Regions::where('level','WITEL')->whereIn('parent_id', $areaIds)->orderBy('name')->get();
            } else {
                $witels = \App\Models\Regions::where('level', 'WITEL')->orderBy('name')->get();
            }
        }

        // STOs: if a witel selected, scope to that parent; otherwise provide all STOs
        if ($witelId) {
            $stos = \App\Models\Regions::where('parent_id', $witelId)->where('level', 'STO')->orderBy('name')->get();
        } else {
            // if regional/area selected but no witel, provide STOs under selected witels
            if ($provinceId) {
                $witelIds = \App\Models\Regions::where('level','WITEL')->where('parent_id',$provinceId)->pluck('id');
                $stos = \App\Models\Regions::where('level','STO')->whereIn('parent_id',$witelIds)->orderBy('name')->get();
            } elseif ($regionalId) {
                $areaIds = \App\Models\Regions::where('level','AREA')->where('parent_id',$regionalId)->pluck('id');
                $witelIds = \App\Models\Regions::where('level','WITEL')->whereIn('parent_id',$areaIds)->pluck('id');
                $stos = \App\Models\Regions::where('level','STO')->whereIn('parent_id',$witelIds)->orderBy('name')->get();
            } else {
                $stos = \App\Models\Regions::where('level', 'STO')->orderBy('name')->get();
            }
        }

        $facilities = \App\Models\Facility::orderBy('name')->get();

        return view('home.mosque.index', compact('mosques', 'regionals', 'provinces', 'witels', 'stos', 'facilities'));
    }
    public function show(Mosque $mosque)
    {
        $mosque->load(['province','city','witel','mosqueFacility.facility','mosqueFacility.photos','photos','activities','cashPositions.photos']);

        // Map facilities to include available flag and note
        $facilities = $mosque->mosqueFacility->map(function ($mf) {
            return [
                'id' => $mf->id,
                'name' => $mf->facility->name ?? $mf->name ?? 'Unknown',
                'is_available' => (bool) $mf->is_available,
                'note' => $mf->note,
            ];
        })->toArray();

        // Enrich mosque object with convenience props used by the view
        $mosque->region_name = $mosque->city->name ?? ($mosque->province->name ?? null);
        $mosque->facilities = $facilities;
        $mosque->cover = $mosque->image_url ?? null;
        $mosque->short_description = Str::limit($mosque->description ?? '', 150);

        // Build images array combining: mosque.cover, mosque photos, facility photos
        $images = [];
        if ($mosque->cover) {
            $images[] = $mosque->cover;
        }

        if ($mosque->relationLoaded('photos') && $mosque->photos->count()) {
            foreach ($mosque->photos as $p) {
                if (!empty($p->url)) {
                    $images[] = $p->url;
                } elseif (!empty($p->path)) {
                    $images[] = Storage::url($p->path);
                }
            }
        }

        if ($mosque->relationLoaded('mosqueFacility') && $mosque->mosqueFacility->count()) {
            foreach ($mosque->mosqueFacility as $mf) {
                if ($mf->relationLoaded('photos') && $mf->photos->count()) {
                    foreach ($mf->photos as $fp) {
                        if (!empty($fp->url)) {
                            $images[] = $fp->url;
                        } elseif (!empty($fp->path)) {
                            $images[] = Storage::url($fp->path);
                        }
                    }
                }
            }
        }

        // Normalize: remove nulls and duplicates, reindex
        $images = array_values(array_filter(array_unique($images)));

        $activities = $mosque->activities ?? collect();
        $cashPositions = $mosque->cashPositions()->where('is_deleted', false)->orderByDesc('period_start')->limit(12)->get();

        return view('home.mosque.detail.index', compact('mosque', 'images', 'activities', 'cashPositions'));
    }

    
}
