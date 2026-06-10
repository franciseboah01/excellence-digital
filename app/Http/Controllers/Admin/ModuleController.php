<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::withCount('formations')->paginate(20);
        return view('admin.modules.index', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'   => 'required|string|max:100|unique:modules,nom',
            'icone' => 'nullable|string|max:10',
        ]);

        Module::create([
            'nom'   => $request->nom,
            'icone' => $request->icone ?? '📚',
        ]);

        return back()->with('success', 'Module créé.');
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'nom'   => 'required|string|max:100|unique:modules,nom,' . $module->id,
            'icone' => 'nullable|string|max:10',
        ]);

        $module->update($request->only('nom', 'icone'));

        return back()->with('success', 'Module modifié.');
    }

    public function destroy(Module $module)
    {
        if ($module->formations()->count() > 0) {
            return back()->with('error', 'Impossible : des formations utilisent ce module.');
        }

        $module->delete();
        return back()->with('success', 'Module supprimé.');
    }
}