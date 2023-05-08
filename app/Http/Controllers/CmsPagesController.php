<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\CmsPagesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;

class CmsPagesController extends Controller
{
    protected $cmsPagesRepository;

    public function __construct(
        CmsPagesRepository $cmsPagesRepository
    ) {
        $this->cmsPagesRepository = $cmsPagesRepository;
    }

    public function index($slug = null, $lang = null)
    {
        $page_title = "FAQ";
        $params['slug'] = $slug;
        $params['response_type'] = "single";
        $cms = $this->cmsPagesRepository->getByParams($params);
        
        return view('cms', compact('file_path','page_title','cms','lang'));
    }
}
