<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PageComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageComponentController extends Controller
{
    /**
     * Constructor - Apply middleware
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\Business::class);
    }
    
    /**
     * Display a listing of the components for a company page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if the user has a company
        if (!$user->company) {
            return redirect()->route('landing.settings')
                ->with('error', __('U moet eerst uw bedrijfspagina instellen.'));
        }
        
        $company = $user->company;
        $components = $company->pageComponents;
        $componentTypes = PageComponent::getComponentTypes();
        
        return view('companies.components.index', compact('company', 'components', 'componentTypes'));
    }
    
    /**
     * Show the form for creating a new component.
     *
     * @param  string  $type
     * @return \Illuminate\Http\Response
     */
    public function create($type)
    {
        $user = Auth::user();
        
        if (!$user->company) {
            return redirect()->route('landing.settings')
                ->with('error', __('U moet eerst uw bedrijfspagina instellen.'));
        }
        
        $company = $user->company;
        $componentTypes = PageComponent::getComponentTypes();
        
        if (!array_key_exists($type, $componentTypes)) {
            return redirect()->route('components.index')
                ->with('error', __('Ongeldig componenttype.'));
        }
        
        return view('companies.components.create', compact('company', 'type', 'componentTypes'));
    }
    
    /**
     * Store a newly created component in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->company) {
            return redirect()->route('landing.settings')
                ->with('error', __('U moet eerst uw bedrijfspagina instellen.'));
        }
        
        $company = $user->company;
          // Validate the request based on component type
        $rules = [
            'type' => 'required|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
        
        switch ($request->type) {
            case 'title':
                $rules['content'] = 'required|string|max:255';
                break;
                
            case 'text':
                $rules['content'] = 'required|string';
                break;
                
            case 'image':
                $rules['image'] = 'required|image|max:2048';
                $rules['settings.alt_text'] = 'nullable|string';
                break;
                
            case 'featured_ads':
                $rules['settings.count'] = 'nullable|integer|min:1|max:8';
                $rules['settings.category'] = 'nullable|string';
                break;
        }
        
        $validatedData = $request->validate($rules);
        
        // Create the component
        $component = new PageComponent();
        $component->company_id = $company->id;
        $component->type = $validatedData['type'];
        $component->sort_order = $validatedData['sort_order'] ?? $company->pageComponents()->count();
        $component->is_active = $request->has('is_active');
          // Handle content based on type
        if ($request->type === 'text' || $request->type === 'title') {
            $component->content = $validatedData['content'];
        }
        
        // Handle settings
        $settings = $request->input('settings', []);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/companies/' . $company->id . '/components');
            $settings['image_path'] = str_replace('public/', 'storage/', $path);
        }
        
        $component->settings = $settings;
        $component->save();
        
        return redirect()->route('components.index')
            ->with('success', __('Component succesvol toegevoegd.'));
    }
    
    /**
     * Show the form for editing the specified component.
     *
     * @param  \App\Models\PageComponent  $component
     * @return \Illuminate\Http\Response
     */
    public function edit(PageComponent $component)
    {
        $user = Auth::user();
        
        // Check if this component belongs to the user's company
        if (!$user->company || $component->company_id != $user->company->id) {
            return redirect()->route('components.index')
                ->with('error', __('U heeft geen toegang tot dit component.'));
        }
        
        $company = $user->company;
        $componentTypes = PageComponent::getComponentTypes();
        
        return view('companies.components.edit', compact('company', 'component', 'componentTypes'));
    }
    
    /**
     * Update the specified component in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PageComponent  $component
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PageComponent $component)
    {
        $user = Auth::user();
        
        // Check if this component belongs to the user's company
        if (!$user->company || $component->company_id != $user->company->id) {
            return redirect()->route('components.index')
                ->with('error', __('U heeft geen toegang tot dit component.'));
        }
        
        // Validate the request based on component type (similar to store method)
        $rules = [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
        
        switch ($component->type) {
            case 'hero':
                $rules['content'] = 'required|string';
                $rules['image'] = 'nullable|image|max:2048';
                $rules['settings.button_text'] = 'nullable|string';
                $rules['settings.button_url'] = 'nullable|string';
                break;
                
            case 'title':
                $rules['content'] = 'required|string|max:255';
                break;
            
            case 'text':
                $rules['content'] = 'required|string';
                break;
                
            case 'image':
                $rules['image'] = 'nullable|image|max:2048';
                $rules['settings.alt_text'] = 'nullable|string';
                break;
                
            case 'featured_ads':
                $rules['settings.count'] = 'nullable|integer|min:1|max:8';
                $rules['settings.category'] = 'nullable|string';
                break;
                
            case 'product_grid':
                $rules['settings.count'] = 'nullable|integer|min:1|max:12';
                $rules['settings.is_rental'] = 'nullable|boolean';
                break;
        }
        
        $validatedData = $request->validate($rules);
        
        // Update the component
        $component->sort_order = $validatedData['sort_order'] ?? $component->sort_order;
        $component->is_active = $request->has('is_active');
          // Handle content based on type
        if ($component->type === 'text' || $component->type === 'title') {
            $component->content = $validatedData['content'];
        }
        
        // Handle settings
        $settings = $component->settings ?? [];
        
        if ($request->has('settings')) {
            foreach ($request->settings as $key => $value) {
                $settings[$key] = $value;
            }
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Remove old image if it exists
            if (isset($settings['image_path'])) {
                $oldPath = str_replace('storage/', 'public/', $settings['image_path']);
                Storage::delete($oldPath);
            }
            
            // Upload new image
            $path = $request->file('image')->store('public/companies/' . $component->company->id . '/components');
            $settings['image_path'] = str_replace('public/', 'storage/', $path);
        }
        
        $component->settings = $settings;
        $component->save();
        
        return redirect()->route('components.index')
            ->with('success', __('Component succesvol bijgewerkt.'));
    }
    
    /**
     * Remove the specified component from storage.
     *
     * @param  \App\Models\PageComponent  $component
     * @return \Illuminate\Http\Response
     */
    public function destroy(PageComponent $component)
    {
        $user = Auth::user();
        
        // Check if this component belongs to the user's company
        if (!$user->company || $component->company_id != $user->company->id) {
            return redirect()->route('components.index')
                ->with('error', __('U heeft geen toegang tot dit component.'));
        }
        
        // Remove image if present
        if (isset($component->settings['image_path'])) {
            $path = str_replace('storage/', 'public/', $component->settings['image_path']);
            Storage::delete($path);
        }
        
        $component->delete();
        
        return redirect()->route('components.index')
            ->with('success', __('Component succesvol verwijderd.'));
    }
    
    /**
     * Update the sorting order of components.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->company) {
            return response()->json(['error' => 'Geen bedrijf gevonden'], 404);
        }
        
        $company = $user->company;
        
        // Validate request
        $request->validate([
            'components' => 'required|array',
            'components.*' => 'integer|exists:page_components,id',
        ]);
        
        // Update order
        $order = 0;
        foreach ($request->components as $componentId) {
            $component = PageComponent::where('id', $componentId)
                ->where('company_id', $company->id)
                ->first();
                
            if ($component) {
                $component->sort_order = $order++;
                $component->save();
            }
        }
        
        return response()->json(['success' => true]);
    }
}
