<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\TemplateCategory;

class TemplateController extends Controller
{
    /**
     * Display a listing of active templates.
     */
    public function index()
    {
        $categories = TemplateCategory::with(['templates' => function ($query) {
            $query->where('is_active', true);
        }])->get();

        $templates = Template::where('is_active', true)->get();

        return view('templates.index', compact('categories', 'templates'));
    }
}
