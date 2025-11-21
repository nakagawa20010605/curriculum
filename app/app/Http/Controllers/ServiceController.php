<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index(){
        $services = Service::where('status', '!=', 3)->orderBy('created_at', 'desc')->paginate(10);
        return view('home', compact('services'));
    }

    public function show(Service $service)
    {
        // ステータス3（削除済）の投稿は、ユーザーからは見えないように404を返す
        if ($service->status === 3) {
            abort(404);
        }
        
        // 詳細ビュー（resources/views/services/show.blade.php）にデータを渡す
        return view('services.show', compact('service'));
    }
}