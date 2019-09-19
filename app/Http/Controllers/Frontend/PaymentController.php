<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use TongVanDuc\NganLuong\Facades\NLBankCharge;
use Validator;

use App\Payment;
use App\Order;
use App\Package;
use App\Course;
use App\User;


class PaymentController extends Controller
{
    public function checkGift(Request $request){
        if ($request->ajax()) {
            $gift = $request->input('gift');
            $courseId = $request->input('course_id');
            $voucher =  DB::table('vouchers')->where('status', 1)->where('code', $gift)->where('course_id', $courseId)->count();
            if($voucher > 0){
                $dataVoucher =  DB::table('vouchers')->where('status', 1)->where('code', $gift)->where('course_id', $courseId)->get()->first();
                if($dataVoucher->buy_course_id){
                    $buy_course_id = $dataVoucher->buy_course_id;
                    $listPay = session('listPay');
                    if(isset($listPay['courses']) && in_array( $buy_course_id, $listPay['courses'])){
                        $request->session()->flash('gift', $dataVoucher->price);
                        return 1;
                    }else{
                        return 3;
                    }

                }else{
                    $request->session()->flash('gift', $dataVoucher->price);
                    return 1;
                }

            }else{
                return 2;
            }
        }
    }
    public function checkGiftCombo(Request $request){
        if ($request->ajax()) {
            $gift = $request->input('gift');
            $packageId = $request->input('package_id');
            $voucher =  DB::table('vouchers')->where('status', 1)->where('code', $gift)->where('package_id', $packageId)->count();
            if($voucher > 0){
                $dataVoucher =  DB::table('vouchers')->where('status', 1)->where('code', $gift)->where('package_id', $packageId)->get()->first();
                $request->session()->flash('giftCombo', $dataVoucher->price);
                return 1;
            }else{
                return 2;
            }
        }
    }
    public function updatePhone(Request $request){
        if ($request->ajax()) {
            $phone = $request->input('phone');
            $user = User::find(Auth::id());
            $user->phone = $phone;
            $user->save();
        }
    }
    public function updateAddress(Request $request){
        if ($request->ajax()) {
            $address = $request->input('address');
            $user = User::find(Auth::id());
            $user->address = $address;
            $user->save();
        }
    }
    public function course(Request $request, $courseId){
        $data['title'] = 'Thanh toán';
        $data['description'] = 'Thanh toán';
        $course = Course::where('id', $courseId)->get()->first();
        $data['course'] = $course;
        $gift = $request->session()->get('gift');
        $price = $course['price'];
        if($course['price_sale']){
            $price = $course['price_sale'];
        }
        if($gift){
            $price = $price - $gift;
        }
        $data['price'] = $price;
        $data['url']='/thanh-toan/'.$courseId.'-'.$course->slug;
        if (Auth::check()) {
            $user = Auth::user();
            if($this->checkPaymentByCourse($user['id'], $courseId)){
                if($this->checkUserByCourse($user['id'], $courseId)){
                    return redirect('/khoa-hoc/'.$course['id'].'-'.$course['slug']);
                }else{
                    return redirect('/gia-han/'.$course['id'].'-'.$course['slug']);
                }
            }else{
                $data['user'] = $user;
                return view('frontend.payment.index', $data);
            }


        }else{
            return view('frontend.payment.login', $data);
        }
    }
    public function paymentCourse(Request $request){
        //dd($request->input());
        //dd(NLBankCharge::ATM($request->input()));

        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'option_payment' => 'required',
        ],[
            'required' => ':attribute không được bỏ trống.',
        ]);

        if($validator->fails()) {
            return redirect('/thanh-toan/'.$request->input('course_id').'-'.$request->input('course_url'))
                        ->withErrors($validator)
                        ->withInput();
        }else{
            if($request->input('option_payment') == 'CK'){
                $userId = Auth::id();
                $key = uniqid();
                //gia han
                $more = $request->input('more');
                $order = [
                    'user_id' => $userId,
                    'course_id' => $request->input('course_id'),
                    'price' => $request->input('total_amount'),
                    'key' => $key,
                    'payment_method' => $request->input('option_payment'),
                    'fullname' => $request->input('fullname'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('mobile'),
                    'address_ship' => $request->input('address'),
                    'orderstatus' => 'onhold',
                    'more' => $more,
                    'status' => 1
                ];
                Order::create($order);
                if($more == 'giahan'){
                    return redirect('/payment/more/'.$key);
                }else{
                    return redirect('/payment/thank/'.$key);
                }



            }else if($request->input('option_payment') == 'ATM_ONLINE'){
                //dd($request->input());
                $nl_result = NLBankCharge::ATM($request->input());
                //dd($nl_result);
                if(isset($nl_result->error_code)){
                    if($nl_result->error_code =='00'){
                        header('Location: '.$nl_result->checkout_url);
                        exit;
                    }else{
                        return redirect('/thanh-toan/'.$request->input('course_id').'-'.$request->input('course_url'))
                        ->withErrors(['bank_code' => 'Lỗi kết nối với Ngân Lượng'])
                        ->withInput();
                    }

                }else{
                    return redirect('/thanh-toan/'.$request->input('course_id').'-'.$request->input('course_url'))
                        ->withErrors(['bank_code' => 'Chưa chọn ngân hàng.'])
                        ->withInput();
                }

            }else if($request->input('option_payment') == 'VISA'){
                $nl_result = NLBankCharge::VISA($request->input());
                if(isset($nl_result->error_code)){
                    if($nl_result->error_code =='00'){
                        header('Location: '.$nl_result->checkout_url);
                        exit;
                    }else{
                        return redirect('/thanh-toan/'.$request->input('course_id').'-'.$request->input('course_url'))
                        ->withErrors(['bank_code' => 'Lỗi kết nối với Ngân Lượng'])
                        ->withInput();
                    }

                }else{
                    return redirect('/thanh-toan/'.$request->input('course_id').'-'.$request->input('course_url'))
                        ->withErrors(['bank_code' => 'Chưa chọn thẻ.'])
                        ->withInput();
                }
            }
        }

    }
    public function successCourse(Request $request, $courseId){
        $course = Course::where('id', $courseId)->get()->first();

        $key = uniqid();
        $order = [
            'course_id' => $courseId,
            'key' => $key,

        ];
        $result = NLBankCharge::TransactionDetail($request->input('token'));
        if(isset($result['status']) && $result['status'] === true){

            $courseTime = $course['time'];
            $xml = $result['data'];
            $json = json_encode($xml);
            $dataNl = json_decode($json,TRUE);

            $price = $dataNl['total_amount'];
            if(Auth::id()){
                $userId = Auth::id();
            }else{
                $userId = $dataNl['user_id'];
            }
            $order['user_id'] = $userId;
            $order['payment_method'] = $dataNl['payment_method'];
            $order['bank_code'] = $dataNl['bank_code'];
            $order['fullname'] = $dataNl['buyer_fullname'];
            $order['email'] = $dataNl['buyer_email'];
            $order['phone'] = $dataNl['buyer_mobile'];
            $order['address_ship'] = $dataNl['buyer_address'];
            $order['price'] = $price;
            $order['status'] = 1;
            $order['orderstatus'] = 'completed';
            if($this->hasPaymentCourse($userId, $courseId)){
                $order['more'] = 'giahan';
                Order::create($order);
            }else{
                Order::create($order);
            }

            //active tai khoan
            $strMonth = '+'.$courseTime.' months';
            $end_date = date('Y-m-d', strtotime($strMonth, strtotime(date('Y-m-d'))));
            if($this->hasPaymentCourse($userId, $courseId)){
                // $payment = $this->hasPaymentCourse($userId, $courseId);
                // $mpay = Payment::find($payment['id']);
                // $mpay->end_date = $end_date;
                // $mpay->save();
                $dataPayment = [
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'price' => $price,
                    'start_date' =>  date('Y-m-d'),
                    'end_date' => $end_date,
                    'status' => 1
                ];
                Payment::create($dataPayment);
                return redirect('/payment/more/'.$key);
            }else{

                $dataPayment = [
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'price' => $price,
                    'start_date' =>  date('Y-m-d'),
                    'end_date' => $end_date,
                    'status' => 1
                ];
                Payment::create($dataPayment);
                return redirect('/payment/thank/'.$key);
            }


        }else{
            return redirect('/');
        }

    }
    public function thank($key){
        $data['title'] = 'Thanh toán thành công';
        $data['description'] = 'Thành toán thành công';
        $order = Order::where('key', $key)->get()->first();
        $data['order'] = $order;
        if($order['payment_method'] == 'ATM_ONLINE' || $order['payment_method'] == 'VISA'){
            $data['message'] = 'Thanh toán thành công! tài khoản của bạn đã được kích hoạt!';
            return  view('frontend.payment.thank', $data);
        }else if($order['payment_method'] == 'CK'){
            $data['message'] = 'Mã đơn hàng của quý khách là <b>'.$order->id.'</b>
            <p>Quý khách vui lòng đến ngân hàng hoặc sử dụng internet banking để chuyển khoản cho chúng tôi theo 1 trong các tài khoản dưới đây. Khi nhận được tiền chúng tôi sẽ ngay lập tức kích hoạt tài khoản cho bạn.</p>
            <p>'.setting('site.bankinfo');
            return  view('frontend.payment.thank', $data);
        }
    }
    public function more($key){
        $data['title'] = 'Gia hạn thành công';
        $data['description'] = 'Gia hạn thành toán';
        $order = Order::where('key', $key)->get()->first();
        $data['order'] = $order;
        if($order['payment_method'] == 'ATM_ONLINE' || $order['payment_method'] == 'VISA'){
            $data['message'] = 'Tài khoản của bạn được gia hạn thành công!';
            return  view('frontend.payment.thank', $data);
        }else if($order['payment_method'] == 'CK'){
            $data['message'] = 'Mã đơn hàng của quý khách là <b>'.$order->id.'</b>
            <p>Quý khách vui lòng đến ngân hàng hoặc sử dụng internet banking để chuyển khoản cho chúng tôi theo 1 trong các tài khoản dưới đây. Khi nhận được tiền chúng tôi sẽ ngay lập tức kích hoạt tài khoản cho bạn.</p>
            <p>'.setting('site.bankinfo');
            return  view('frontend.payment.thank', $data);
        }
    }

    public function combo(Request $request, $packageId){
        $data['title'] = 'Thanh toán';
        $data['description'] = 'Thanh toán';
        $package = Package::where('id', $packageId)->get()->first();
        $data['package'] = $package;
        $gift = $request->session()->get('giftCombo');
        $price = $package['price'];
        if($package['price_sale']){
            $price = $package['price_sale'];
        }
        if($gift){
            $price = $price - $gift;
        }
        $data['price'] = $price;
        $data['url']='/thanh-toan/package/'.$packageId.'-'.$package->slug;
        if (Auth::check()) {
            $user = Auth::user();
            if($this->checkPaymentByPackage($user['id'], $packageId)){
                if($this->checkUserByPackage($user['id'], $packageId)){
                    return redirect('/khoa-hoc/package/'.$package['id'].'-'.$package['slug']);
                }else{
                    return redirect('/gia-han/package/'.$package['id'].'-'.$package['slug']);
                }
            }else{
                $data['user'] = $user;
                return view('frontend.payment.combo', $data);
            }

        }else{
            return view('frontend.payment.logincombo', $data);
        }
    }
    public function paymentPackage(Request $request){
        //dd($request->input());
        //dd(NLBankCharge::ATM($request->input()));

        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'option_payment' => 'required',
        ],[
            'required' => ':attribute không được bỏ trống.',
        ]);

        if($validator->fails()) {
            return redirect('/thanh-toan/package/'.$request->input('package_id').'-'.$request->input('course_url'))
                        ->withErrors($validator)
                        ->withInput();
        }else{
            if($request->input('option_payment') == 'CK'){
                $userId = Auth::id();
                $key = uniqid();
                $more = $request->input('more');
                $order = [
                    'user_id' => $userId,
                    'package_id' => $request->input('package_id'),
                    'price' => $request->input('total_amount'),
                    'key' => $key,
                    'payment_method' => $request->input('option_payment'),
                    'fullname' => $request->input('fullname'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('mobile'),
                    'address_ship' => $request->input('address'),
                    'orderstatus' => 'onhold',
                    'more' => $more,
                    'status' => 1
                ];
                //dd($order);
                Order::create($order);
                if($more == 'giahan'){
                    return redirect('/payment/more/'.$key);
                }else{
                    return redirect('/payment/thank/'.$key);
                }


            }else if($request->input('option_payment') == 'ATM_ONLINE'){
                //dd($request->input());
                $nl_result = NLBankCharge::ATM($request->input());
                //dd($nl_result);
                if(isset($nl_result->error_code)){
                    if($nl_result->error_code =='00'){
                        header('Location: '.$nl_result->checkout_url);
                        exit;
                    }else{
                        return redirect('/thanh-toan/package/'.$request->input('package_id').'-'.$request->input('package_url'))
                        ->withErrors(['bank_code' => 'Lỗi kết nối với Ngân Lượng'])
                        ->withInput();
                    }

                }else{
                    return redirect('/thanh-toan/package/'.$request->input('package_id').'-'.$request->input('package_url'))
                        ->withErrors(['bank_code' => 'Chưa chọn ngân hàng.'])
                        ->withInput();
                }

            }else if($request->input('option_payment') == 'VISA'){
                $nl_result = NLBankCharge::VISA($request->input());
                if(isset($nl_result->error_code)){
                    if($nl_result->error_code =='00'){
                        header('Location: '.$nl_result->checkout_url);
                        exit;
                    }else{
                        return redirect('/thanh-toan/package/'.$request->input('package_id').'-'.$request->input('package_url'))
                        ->withErrors(['bank_code' => 'Lỗi kết nối với Ngân Lượng'])
                        ->withInput();
                    }

                }else{
                    return redirect('/thanh-toan/package/'.$request->input('package_id').'-'.$request->input('package_url'))
                        ->withErrors(['bank_code' => 'Chưa chọn thẻ.'])
                        ->withInput();
                }
            }
        }

    }
    public function successPackage(Request $request, $packageId){
        //dd($packageId);
        $package = Package::where('id', $packageId)->get()->first();

        $key = uniqid();
        $order = [
            'package_id' => $packageId,
            'key' => $key,

        ];
        $result = NLBankCharge::TransactionDetail($request->input('token'));
        if(isset($result['status']) && $result['status'] === true){
            $packageTime = $package['time'];
            $xml = $result['data'];
            $json = json_encode($xml);
            $dataNl = json_decode($json,TRUE);
            $price = $dataNl['total_amount'];
            if(Auth::id()){
                $userId = Auth::id();
            }else{
                $userId = $dataNl['user_id'];
            }
            $order['user_id'] = $userId;
            $order['payment_method'] = $dataNl['payment_method'];
            $order['bank_code'] = $dataNl['bank_code'];
            $order['fullname'] = $dataNl['buyer_fullname'];
            $order['email'] = $dataNl['buyer_email'];
            $order['phone'] = $dataNl['buyer_mobile'];
            $order['address_ship'] = $dataNl['buyer_address'];
            $order['price'] = $price;
            $order['status'] = 1;
            $order['orderstatus'] = 'completed';

            if($this->hasPaymentPackage($userId, $packageId)){
                $order['more'] = 'giahan';
                Order::create($order);
            }else{
                Order::create($order);
            }

            //active tai khoan
            $strMonth = '+'.$packageTime.' months';
            $end_date = date('Y-m-d', strtotime($strMonth, strtotime(date('Y-m-d'))));
            //gia han
            if($this->hasPaymentPackage($userId, $packageId)){
                // $payment = $this->hasPaymentPackage($userId, $packageId);
                // $mpay = Payment::find($payment['id']);
                // $mpay->end_date = $end_date;
                // $mpay->price = $end_date;
                // $mpay->save();
                $payment = [
                    'user_id' => $userId,
                    'package_id' => $packageId,
                    'price' => $price,
                    'start_date' =>  date('Y-m-d'),
                    'end_date' => $end_date,
                    'status' => 1
                ];
                Payment::create($payment);
                return redirect('/payment/more/'.$key);
            }else{
                $payment = [
                    'user_id' => $userId,
                    'package_id' => $packageId,
                    'price' => $price,
                    'start_date' =>  date('Y-m-d'),
                    'end_date' => $end_date,
                    'status' => 1
                ];
                Payment::create($payment);

                return redirect('/payment/thank/'.$key);
            }
        }else{
            return redirect('/');
        }

    }
    public function moreCourse($courseId){
        $data['title'] = 'Gia hạn khóa học';
        $data['description'] = 'Gia hạn khóa học';
        $course = Course::where('id', $courseId)->get()->first();
        $data['course'] = $course;
        $data['url']='/thanh-toan/'.$courseId.'-'.$course->slug;
        if (Auth::check()) {
            $user = Auth::user();
            $data['user'] = $user;
            $data['more'] = 'giahan';
            return view('frontend.payment.index', $data);
        }else{
            return view('frontend.payment.login', $data);
        }
    }
    public function moreCombo($packageId){
        $data['title'] = 'Gia hạn khóa học';
        $data['description'] = 'Gia hạn combo khóa học';
        $package = Package::where('id', $packageId)->get()->first();
        $data['package'] = $package;
        $data['url']='/thanh-toan/package/'.$packageId.'-'.$package->slug;
        if (Auth::check()) {
            $user = Auth::user();
            $data['user'] = $user;
            $data['more'] = 'giahan';
            return view('frontend.payment.combo', $data);
        }else{
            return view('frontend.payment.logincombo', $data);
        }
    }
    public function checkPaymentByCourse($userId, $courseId){

        $checkPayment = Payment::where(['user_id' => $userId, 'course_id' =>  $courseId, 'status' => 1])->get()->count();
        if($checkPayment > 0){
            return true;
        }else{
            $packages =  DB::table('course_package')->select('package_id')->where('course_id', $courseId)->get();

            if($packages){
                $packageIds = [];
                foreach($packages as $package){
                    $packageIds[] = $package->package_id;
                }

                $checkPackagePayment = Payment::where(['user_id' => $userId, 'status' => 1])->whereIn('package_id', $packageIds)->get()->count();
                if($checkPackagePayment >0 ){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }

        }
    }
    public function checkUserByCourse($userId, $courseId){
        $dateNow = date('Y-m-d');
        $checkUser = Payment::where(['user_id' => $userId, 'course_id' =>  $courseId, 'status' => 1])->where('end_date', '>=', $dateNow)->get()->count();
        if($checkUser > 0){
            return true;
        }else{
            $packages =  DB::table('course_package')->select('package_id')->where('course_id', $courseId)->get();
            //dd($packages);
            if($packages){
                $packageIds = [];
                foreach($packages as $package){
                    $packageIds[] = $package->package_id;
                }
                $checkPackageUser = DB::table('payments')->where(['user_id' => $userId, 'status' => 1])->whereIn('package_id', $packageIds)->where('end_date', '>=', $dateNow)->get()->count();
                if($checkPackageUser > 0 ){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }

        }
    }
    public function checkPaymentByPackage($userId, $packageId){

        $checkPayment = Payment::where(['user_id' => $userId, 'package_id' =>  $packageId, 'status' => 1])->get()->count();
        if($checkPayment > 0){
            return true;
        }else{
            return false;
        }
    }
    public function checkUserByPackage($userId, $packageId){
        $dateNow = date('Y-m-d');
        $checkUser = Payment::where(['user_id' => $userId, 'package_id' =>  $packageId, 'status' => 1])->where('end_date', '>=', $dateNow)->get()->count();
        if($checkUser > 0){
            return true;
        }else{
            return false;
        }
    }
    public function hasPaymentCourse($userId, $courseId){
        $checkPayment = Payment::where(['user_id' => $userId, 'course_id' =>  $courseId, 'status' => 1])->get()->count();
        if($checkPayment > 0){
            return Payment::where(['user_id' => $userId, 'course_id' =>  $courseId, 'status' => 1])->get()->first()->toArray();
        }else{
            return false;
        }
    }
    public function hasPaymentPackage($userId, $packageId){
        $checkPayment = Payment::where(['user_id' => $userId, 'package_id' =>  $packageId, 'status' => 1])->get()->count();
        if($checkPayment > 0){
            return Payment::where(['user_id' => $userId, 'package_id' =>  $packageId, 'status' => 1])->get()->first()->toArray();
        }else{
            return false;
        }
    }
}
