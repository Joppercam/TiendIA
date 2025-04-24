<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::latest()->paginate(15);
        
        return view('admin.campaigns.index', compact('campaigns'));
    }
    
    public function create()
    {
        return view('admin.campaigns.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_link' => 'nullable|url',
            'slug' => 'nullable|string|unique:campaigns,slug',
        ]);
        
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        
        $bannerImage = null;
        if ($request->hasFile('banner_image')) {
            $bannerImage = $request->file('banner_image')->store('campaigns', 'public');
        }
        
        $campaign = Campaign::create([
            'name' => $request->name,
            'description' => $request->description,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_active' => $request->has('is_active'),
            'banner_image' => $bannerImage,
            'banner_link' => $request->banner_link,
            'slug' => $slug,
        ]);
        
        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Campaña creada correctamente.');
    }
    
    public function show(Campaign $campaign)
    {
        $promotions = $campaign->promotions()->paginate(10);
        
        return view('admin.campaigns.show', compact('campaign', 'promotions'));
    }
    
    public function edit(Campaign $campaign)
    {
        return view('admin.campaigns.edit', compact('campaign'));
    }
    
    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_link' => 'nullable|url',
            'slug' => 'nullable|string|unique:campaigns,slug,'.$campaign->id,
        ]);
        
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        
        $bannerImage = $campaign->banner_image;
        if ($request->hasFile('banner_image')) {
            // Eliminar imagen anterior si existe
            if ($campaign->banner_image) {
                Storage::disk('public')->delete($campaign->banner_image);
            }
            $bannerImage = $request->file('banner_image')->store('campaigns', 'public');
        }
        
        $campaign->update([
            'name' => $request->name,
            'description' => $request->description,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'is_active' => $request->has('is_active'),
            'banner_image' => $bannerImage,
            'banner_link' => $request->banner_link,
            'slug' => $slug,
        ]);
        
        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Campaña actualizada correctamente.');
    }
    
    public function destroy(Campaign $campaign)
    {
        // Eliminar imagen si existe
        if ($campaign->banner_image) {
            Storage::disk('public')->delete($campaign->banner_image);
        }
        
        $campaign->delete();
        
        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Campaña eliminada correctamente.');
    }
}