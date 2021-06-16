<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Theme;

class Themes extends Controller
{
    public function index() {
        $user_id = Auth::user()->id;
        $shopify_themes = $this->getAllThemes();
        $themes = array_map(function ($theme) {
            return $theme['shopify_theme_id'];
        }, Theme::select('shopify_theme_id')->get()->toArray());

        $shopify_theme_ids = array();

        foreach($shopify_themes as $shopify_theme) {
            if(in_array($shopify_theme['id'], $themes)) {
                Theme::where('shopify_theme_id', $shopify_theme['id'])
                    ->update(['name' => $shopify_theme['name'], 'role' => $shopify_theme['role']]);
            } else {
                $theme = new Theme;

                $theme->shopify_theme_id = $shopify_theme['id'];
                $theme->name = $shopify_theme['name'];
                $theme->role = $shopify_theme['role'];
                $theme->user_id = $user_id;

                $theme->save();
            }

            array_push($shopify_theme_ids, $shopify_theme['id']);
        }

        foreach($themes as $theme_id) {
            if(!in_array($theme_id, $shopify_theme_ids)) {
                Theme::where('shopify_theme_id', $theme_id)->delete();
            }
        }

        $themes = array_map(function ($theme) {
            return ['value' => $theme['shopify_theme_id'], 'label' => $theme['name']];
        }, Theme::all()->toArray());

        return response()->json(['themes' => $themes]);
    }

    public function getAllThemes() {
        $shop = Auth::user();
        $shopApi = $shop->api();

        $themes = $shopApi->rest('GET', 'admin/themes.json')['body']['container']['themes'];
        
        return $themes;
    }
    
}
