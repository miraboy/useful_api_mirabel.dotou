<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortLink;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class UrlShortenerController extends Controller
{
    public function shorten(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
            'custom_code' => 'nullable|string|max:10|alpha_dash|unique:shortlinks,code'
        ]);

        $code = $request->custom_code ?? Str::random(6);

        $url = ShortLink::create([
            'user_id' => Auth::id(),
            'original_url' => $request->original_url,
            'code' => $code,
            'clicks' => 0
        ]);
        $id = $url->id;
        $url = Auth::user()->shortlinks()
            ->select('id','user_id', 'original_url', 'code', 'clicks', 'created_at')
            ->where('code', $code)
            ->get()
            ->makeHidden(['updated_at']);
        return response()->json($url, 201);
    }

    public function redirect($code)
    {
        $url = ShortLink::where('code', $code)->firstOrFail();
        $url->increment('clicks');
        return redirect($url->original_url);
    }

    public function index()
    {
        $urls = Auth::user()->shortlinks()
            ->select('id','user_id', 'original_url', 'code', 'clicks', 'created_at')
            ->get()
            ->makeHidden(['updated_at']);
        return response()->json($urls, 200);
    }

    public function destroy($id)
    {
        $url = Auth::user()->shortlinks()->findOrFail($id);
        $url->delete();
        return response()->json(['message' => 'Link deleted successfully'], 200);
    }
}   