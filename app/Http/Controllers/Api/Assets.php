<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Asset;
use MatthiasMullie\Minify;

class Assets extends Controller
{
    public function index($theme_id) {
        $shopify_assets = $this->getAllAssets($theme_id);
        $asset_filenames = array_map(function ($asset) {
            return $asset['asset_key'];
        }, Asset::select('asset_key')->where('theme_id', $theme_id)->get()->toArray());
        $assets = Asset::where('theme_id', $theme_id)->get()->toArray();
        $js_filenames = [];
        $js_files = [];

        foreach($shopify_assets as $asset) {
            if(substr($asset['key'], 0, 6) == 'assets' && $asset['content_type'] == 'application/javascript') {
                array_push($js_filenames, substr($asset['key'], 7));
                array_push($js_files, $asset);
            }
        }

        foreach($js_filenames as $key => $filename) {
            if(!in_array($filename, $asset_filenames)) {
                $file_content = $this->getFileContent($theme_id, $js_files[$key]['key']);
                Storage::put($theme_id . '\/original\/' . $filename, $file_content);
                $path = $theme_id . '\/original\/' . $filename;
                $asset = new Asset;
                $asset->asset_key = $filename;
                $asset->theme_id = $theme_id;
                $asset->original_file_url = $path;
                $asset->status = '0';

                $asset->save();
            } else {
                $asset = Asset::where('theme_id', $theme_id)->where('asset_key', $filename)->get();
                if($asset[0]['status'] != '1') {
                    $file_content = $this->getFileContent($theme_id, $js_files[$key]['key']);
                    Storage::put($theme_id . '\/original\/' . $filename, $file_content);
                }
            }
        }

        foreach($assets as $asset) {
            if(!in_array($asset['asset_key'], $js_filenames)) {
                Storage::delete($theme_id . '\/original\/' . $asset['asset_key']);
                Storage::delete($theme_id . '\/minified\/' . $asset['asset_key']);
                Asset::where('theme_id', $theme_id)->where('asset_key', $asset['asset_key'])->delete();
            }
        }

        $data = Asset::select(['id', 'asset_key', 'status'])->where('theme_id', $theme_id)->get()->toArray();
        $preview_url = "https://" . Auth::user()->name . '?preview_theme_id=' . $theme_id;

        return response()->json(['rows' => $data, 'previewLink' => $preview_url]);
    }

    public function minify($asset_id) {
        $asset = Asset::find($asset_id);
        $original_content = Storage::get($asset->original_file_url);
        $minifier = new Minify\JS($original_content);
        $minified_file_url = $asset->theme_id . '\/minified\/' . $asset->asset_key;
        $content = $minifier->minify();
        Storage::put($minified_file_url, $content);
        $this->write_asset($asset->theme_id, $asset->asset_key, $content);
        $asset->status = '1';
        $asset->minified_file_url = $minified_file_url;

        $asset->save();
        return $this->index($asset->theme_id);
    }

    public function revert($asset_id) {
        $asset = Asset::find($asset_id);
        $original_content = Storage::get($asset->original_file_url);
        
        $this->write_asset($asset->theme_id, $asset->asset_key, $original_content);
        $asset->status = '0';

        $asset->save();
        return $this->index($asset->theme_id);
    }

    public function minifyAll($theme_id) {
        $assets = Asset::where('status', '0')->get();
        foreach($assets as $asset) {
            $original_content = Storage::get($asset->original_file_url);
            $minifier = new Minify\JS($original_content);
            $minified_file_url = $theme_id . '\/minified\/' . $asset->asset_key;
            $content = $minifier->minify();
            Storage::put($minified_file_url, $content);
            $this->write_asset($asset->theme_id, $asset->asset_key, $content);
            $asset->status = '1';
            $asset->minified_file_url = $minified_file_url;

            $asset->save();
        }

        return $this->index($theme_id);
    }

    public function revertAll($theme_id) {
        $assets = Asset::where('status', '1')->get();
        foreach($assets as $asset) {
            $original_content = Storage::get($asset->original_file_url);
            $this->write_asset($theme_id, $asset->asset_key, $original_content);
            $asset->status = '0';

            $asset->save();
        }

        return $this->index($theme_id);
    }

    public function write_asset($theme_id, $asset_key, $content) {
        $shop = Auth::user();
        $shopApi = $shop->api();

        $response = $shopApi->rest('PUT', 'admin/themes/' . $theme_id . '/assets.json', [
            "asset" => [
                "key" => "assets/" . $asset_key,
                "value" => $content
            ]
        ]);
    }

    public function getAllAssets($theme_id) {
        $shop = Auth::user();
        $shopApi = $shop->api();

        $assets = $shopApi->rest('GET', 'admin/themes/' . $theme_id . '/assets.json')['body']['container']['assets'];

        return $assets;
    }

    public function getFileContent($theme_id, $key) {
        $shop = Auth::user();
        $shopApi = $shop->api();

        $content = $shopApi->rest('GET', 'admin/themes/' . $theme_id . '/assets.json', [
            "asset" => [
                "key" => $key
            ]
        ])['body']['container']['asset']['value'];

        return $content;
    }
}
