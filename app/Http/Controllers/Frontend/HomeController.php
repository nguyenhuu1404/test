<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Banner;
use App\Package;
use App\Lesson;
use App\Course;
use App\Review;

class HomeController extends Controller
{

    function index(){
        $data['banners'] = Banner::orderBy('order', 'desc')->where('status', 1)->get();

        $data['packageRecomands'] = Package::where('status', 1)->where('is_home', 1)->orderBy('order', 'asc')->get();
        $data['courseRecomands'] = Course::where('status', 1)->where('is_home', 1)->orderBy('order', 'asc')->get();

        $trialLessons = Lesson::where(['status'=> 1, 'is_home'=>1, 'trial' => 1])->get();
        $courseTrialLesson = [];
        foreach($trialLessons as $lesson){
            $courseTrialLesson[$lesson['id']] = $lesson->courses()->get()->toArray();
        }
        $data['trialLessons'] = $trialLessons->toArray();
        $data['courseTrialLesson'] = $courseTrialLesson;

        $courses = Course::where(['status' => 1])->get()->toArray();
        $dataCourses = [];
        if(count($courses) > 0){
            foreach($courses as $course){
                if($course['type']){
                    $dataCourses[strtolower($course['type'])][] = $course;
                }
            }
        }
        $data['reviews'] = Review::where('status', 1)->get();
        $data['title'] = setting('site.title');
        $data['description'] = setting('site.description');
        $data['courses'] = $dataCourses;
        $data['courseTypes'] = config('app.courseTypes');

        return view('frontend.home.index', $data);
    }
    public function relogin(){
        $data['title'] = "Đăng nhập tài khoản cùng lúc";
        $data['description'] = setting('site.description');
        return view('frontend.alerts.relogin', $data);
    }
}
