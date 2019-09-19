@extends('frontend.layouts.master')
@section('content')
    @isset($banners)
        <section class="banner">
            @foreach ($banners as $banner)
                <div>
                    <img src="{{ Storage::url($banner['image']) }}">
                    @if($banner['text_button'])
                    <div class="title-description">
                        <div class="container">
                            <h2 class="title-slider">{{ $banner['title'] }}</h2>
                            <a href="" class="btn-slider ml-3">{{ $banner['text_button']}}</a>
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach

        </section>
    @endisset

@isset($packageRecomands)
    @foreach($packageRecomands as $packageRecomand)
    @php
    $sale = false;
    if(isset($packageRecomand['price_sale']) && isset($packageRecomand['price'])){
        $sale = floor(($packageRecomand['price_sale'] * 100) / $packageRecomand['price']);
    }

    @endphp
    <section class="combo-box">
        <div class="container">
            <div class="row">
                <div class="col-xl-7 col-lg-7 d-flex">
                    <div class="thumbnail-img">
                        <img src="{{ Storage::url($packageRecomand['image']) }}" alt="">
                    </div>
                    <div class="content-combo">
                        <h4 class="title-combo">{{$packageRecomand['name']}}</h4>
                        @if($sale)
                        <span class="sale-combo">SALE {{100-$sale}}%</span>
                        @endif
                        <div class="info-combo">
                            <div class="item-info-combo">
                                <i><img src="/images/icon-hours.png" alt="icon"></i>
                                Thời hạn: {{$packageRecomand['time']}} tháng
                            </div>
                            <div class="item-info-combo">
                                <i><img src="/images/icon-video.png" alt="icon"></i>
                                Video: {{$packageRecomand['video_number']}}
                            </div>
                            <div class="item-info-combo">
                                <i><img src="/images/icon-3code.png" alt="icon"></i>
                                Mã số: {{$packageRecomand['code']}}
                            </div>
                            <div class="item-info-combo">
                                <i><img src="/images/icon-file.png" alt="icon"></i>
                                Bài test: {{$packageRecomand['test_number']}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div class="price-combo">
                    @if($packageRecomand['price'] > 0 && $packageRecomand['price_sale'] > 0)
                        @if($packageRecomand['price'] > 0)
                        <div class="sale-price-combo">{{ number_format($packageRecomand['price'], 0, ',', '.') }} vnđ</div>
                        @endif
                        @if($packageRecomand['price_sale'] > 0)
                        <p class="text-only-price-combo">chỉ còn</p>
                        <div class="after-salce-price-combo">{{ number_format($packageRecomand['price_sale'], 0, ',', '.') }} vnđ</div>
                        @endif
                    @else
                    @if($packageRecomand['price'] > 0)
                        <div class="after-salce-price-combo">{{ number_format($packageRecomand['price'], 0, ',', '.') }} vnđ</div>
                        @endif
                    @endif

                        @if($packageRecomand['lock'] == 1)
                        <div class="group-btn-combo">
                            <button onclick="lockMessage()" class="btn transition btn-more mr-3">XEM CHI TIẾT</button>
                            <button onclick="lockMessage()" class="btn transition btn-buy">MUA KHÓA HỌC</button>
                        </div>
                        @else
                        <div class="group-btn-combo">
                            <a href="/khoa-hoc/package/{{ $packageRecomand['id'] }}-{{ $packageRecomand['slug'] }}" class="btn transition btn-more mr-3">XEM CHI TIẾT</a>
                            <a href="/thanh-toan/package/{{ $packageRecomand['id'] }}-{{ $packageRecomand['slug'] }}" class="btn transition btn-buy">MUA KHÓA HỌC</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endforeach
@endisset

@isset($courseRecomands)
@foreach($courseRecomands as $courseRecomand)
    @php
    $sale = false;
    if(isset($courseRecomand['price_sale']) && isset($courseRecomand['price'])){
        $sale = floor(($courseRecomand['price_sale'] * 100) / $courseRecomand['price']);
    }
    @endphp
    <section class="combo-box">
        <div class="container">
            <div class="row">
                <div class="col-xl-7 col-lg-7 d-flex">
                    <div class="thumbnail-img">
                        <img src="{{ Storage::url($courseRecomand['image']) }}" alt="">
                    </div>
                    <div class="content-combo">
                        <h4 class="title-combo">{{$courseRecomand['name']}}</h4>
                        @if($sale)
                        <span class="sale-combo">SALE {{100-$sale}}%</span>
                        @endif
                        <div class="info-combo">
                            <div class="item-info-combo">
                                <i><img src="/images/icon-hours.png" alt="icon"></i>
                                Thời hạn: {{$courseRecomand['time']}} tháng
                            </div>
                            <div class="item-info-combo">
                                <i><img src="/images/icon-video.png" alt="icon"></i>
                                Video: {{$courseRecomand['video_number']}}
                            </div>
                            <div class="item-info-combo">
                                <i><img src="/images/icon-3code.png" alt="icon"></i>
                                Mã số: {{$courseRecomand['code']}}
                            </div>
                            <div class="item-info-combo">
                                <i><img src="/images/icon-file.png" alt="icon"></i>
                                Bài test: {{$courseRecomand['test_number']}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div class="price-combo">
                    @if($courseRecomand['price'] > 0 && $courseRecomand['price_sale'] > 0)

                        @if($courseRecomand['price'] > 0)
                        <div class="sale-price-combo">{{ number_format($courseRecomand['price'], 0, ',', '.') }} vnđ</div>
                        @endif
                        @if($courseRecomand['price_sale'] > 0)
                        <p class="text-only-price-combo">chỉ còn</p>
                        <div class="after-salce-price-combo">{{ number_format($courseRecomand['price_sale'], 0, ',', '.') }} vnđ</div>
                        @endif
                    @else
                        @if($courseRecomand['price'] > 0)
                        <div class="after-salce-price-combo">{{ number_format($courseRecomand['price'], 0, ',', '.') }} vnđ</div>
                        @endif
                    @endif

                        @if($courseRecomand['lock'] == 1)
                        <div class="group-btn-combo">
                            <button onclick="lockMessage()" class="btn transition btn-more mr-3">XEM CHI TIẾT</button>
                            <button onclick="lockMessage()" class="btn transition btn-buy">MUA KHÓA HỌC</button>
                        </div>
                        @else
                        <div class="group-btn-combo">
                            <a href="/khoa-hoc/{{ $courseRecomand['id'] }}-{{ $courseRecomand['slug'] }}" class="btn transition btn-more mr-3">XEM CHI TIẾT</a>
                            <a href="/thanh-toan/{{ $courseRecomand['id'] }}-{{ $courseRecomand['slug'] }}" class="btn transition btn-buy">MUA KHÓA HỌC</a>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
    @endforeach
@endisset

            @php
                $href = str_replace('\\', '/', setting('gioi-thieu.background_img'));
                $link_youtube = setting('gioi-thieu.youtube');
                $youtubeId = substr($link_youtube, strrpos($link_youtube, '=') + 1);
            @endphp
            <style>
                .introduce {
                    background-image: url( "/storage/{{ $href }}") !important;
                    background-color: "#{{ setting('gioi-thieu.background_color') }}" !important;
                }
            </style>
			<section class="introduce">
				<div class="container">
					<div class="row">
						<div class="col-xl-3 col-lg-4">
							<div class="title-introduce">
								{{ setting('gioi-thieu.title') }}
							</div>
							<div class="h2">
                                {{ setting('gioi-thieu.name') }}
							</div>
							<p>
                            {{ setting('gioi-thieu.description') }}
                            </p>
							<a href="{{ setting('gioi-thieu.href') }}" class="btn-classroom mt-5">{{ setting('gioi-thieu.button') }}</a>
						</div>
						<div class="col-xl-9 col-lg-8">
							<iframe class="iframe" height="450" src="https://www.youtube.com/embed/{{$youtubeId}}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>
					</div>
				</div>
			</section>

		<section class="courses-box">
				<div class="container">
					<div class="row">
						<div class="col-xl-12">
							<div class="title-course">
								<p>Các khóa học online hiện có trên</p>
								<div class="h4">TIẾNG NHẬT CÔ LAM</div>
								<div class="checkbox-courses">
									<label class="p-0">
										<input id="packageType" value="single" type="checkbox" data-toggle="toggle">
										<div class="toogle-left-right"></div>
										<div onclick="getPackages()" class="package-toggle">Gói lẻ</div>
										<div onclick="getPackages()" class="combo-toggle">Combo</div>
									</label>
								</div>
							</div>
							<div id="resultPackage" class="class-box">

								<ul class="nav nav-tabs d-flex justify-content-center" id="myTab" role="tablist">
                                    @foreach($courseTypes as $courseType)
									<li class="nav-item">
										<a class="nav-link text-uppercase {{$courseType == 'n5' ? 'active' : ''}}" id="{{$courseType}}-tab" data-toggle="tab" href="#{{$courseType}}" role="tab" aria-controls="{{$courseType}}" aria-selected="false">{{$courseType}}</a>
									</li>
									@endforeach
								</ul>
								<div class="tab-content" id="myTabContent">
                                    @foreach($courseTypes as $courseType)
									<div class="tab-pane fade {{$courseType == 'n5' ? 'show active' : ''}}" id="{{$courseType}}" role="tabpanel" aria-labelledby="profile-tab">

										<div class="owl-carousel owl-theme owl-3-items" id="huu4">
                                            @if(isset($courses[$courseType]) >0 )
                                                @foreach($courses[$courseType] as $key => $course)
                                                <div class="item-class {{ $key % 2 !=0 ? 'yellow' : ''}}">
                                                    <div class="thumbnail-tab-class">
                                                        <img src="{{ Storage::url($course['image']) }}">
                                                        <div class="info-class-position">
                                                            <div class="h3">{{$course['name']}}</div>
                                                            <p>{{$course['time']}} tháng</p>
                                                        </div>
                                                    </div>
                                                    <div class="body-item-class">
                                                        <div class="title-body-item-class">
                                                        {{$course['name']}} @if($course['title']) - @endif {{$course['title']}}
                                                            <span class="tuition fs18">học phí: <b>{{$course['price_sale'] ? priceFormat($course['price_sale']) : priceFormat($course['price'])}}</b>

                                                            @if($course['price_sale'])
                                                            <b class="sale-price">{{priceFormat($course['price'])}}</b>
                                                            @endif
                                                            </span>
                                                        </div>
                                                        <div class="content-item-class">
                                                            <div class="info-item-class">
                                                                <div class="duration">L</div>
                                                                Thời gian học:  {{$course['time']}} tháng
                                                            </div>
                                                            <div class="info-item-class">
                                                                <div class="number-video"></div>
                                                                Số video:  {{$course['video_number']}}
                                                            </div>
                                                            @if($course['lock'] == 1)
                                                            <div class="group-btn-item-class">
                                                                <button onclick="lockMessage()" class="btn transition btn-more mr-2">CHI TIẾT</button>
                                                                <button onclick="lockMessage()" class="btn transition btn-buy">MUA KHÓA HỌC</button>
                                                            </div>
                                                            @else
                                                            <div class="group-btn-item-class">
                                                                <a href="/khoa-hoc/{{$course['id']}}-{{$course['slug']}}" class="btn transition btn-more mr-2">CHI TIẾT</a>
                                                                <a href="/thanh-toan/{{$course['id']}}-{{$course['slug']}}" class="btn transition btn-buy">MUA KHÓA HỌC</a>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @endif

										</div>

                                    </div>
                                    @endforeach

								</div>
							</div>
						</div>
					</div>
				</div>
			</section>



            @if($trialLessons)
			<section class="try-it">
				<div class="container">
					<div class="row">
						<div class="col-xl-12">
							<div-- class="header-try">
								<div class="title-try-it">
									HỌC THỬ
									<span>Tiếng nhật cô lam</span>
								</div>
								<!--div class="cate-try-it">
									Phân loại:
									<select>
										<option>Ngữ pháp</option>
									</select>
								</div-->
							</div>
							<div class="body-try w-100">
								<div class="row">
									<div class="col-xl-6 pointer left-try-it">
                                        @foreach($trialLessons as $indexKey => $trialLesson)
                                        <?php

                                             $link_youtube = $trialLesson['youtube'];
                                             $youtubeId = substr($link_youtube, strrpos($link_youtube, '=') + 1);
                                        ?>
										<div onclick="videoTrial(this, '<?= $youtubeId ?>')" class="item-try-it {{ ($indexKey == 0) ? 'active' : '' }}">
											<div class="thumbnail-tryit">
												<img src="{{ Storage::url($trialLesson['image']) }}">
                                            </div>

											<div class="content-try-it">
												<div class="h4">
                                                    <?php
                                                    $courses = $courseTrialLesson[$trialLesson['id']];
                                                    if(count($courses) > 0){
                                                        $courseName = [];
                                                        foreach($courses as $course){
                                                            $courseName[] = $course['name'];
                                                        }
                                                        echo implode(', ', $courseName);
                                                    }
                                                    ?>
                                                </div>
												<p class="tilte-post">{{$trialLesson['name']}}</p>
                                            </div>

										</div>
										@endforeach

									</div>
									<div class="col-xl-6 right-try-it">
										<div class="video-tryit">

                                        <iframe id="videoTrial" class="w-100" height="460px;" src="https://www.youtube.com/embed/{{ getYoutubeId($trialLessons[0]['youtube'])}}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
										</div>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
				</div>
            </section>
            <script>
                function videoTrial(that, youtubeId){
                    $('#videoTrial').attr('src', 'https://www.youtube.com/embed/'+youtubeId);
                    $('.item-try-it').removeClass('active');
                    $(that).addClass('active');
                }
            </script>
            @endif
			<section class="course-payment">
				<div class="container">
					<div class="row">
						<div class="col-xl-12 d-flex align-items-center">
							<img src="/images/icon-payment.png">
							<div class="text-course-payment">
								<div class="h4">Các phương thức thanh toán khóa học</div>
								<p>Contact us now and we will make your event unique & unforgettable</p>
							</div>
							<a href="/phuong-thuc-thanh-toan" class="btn transition btn-more more-payment">XEM CHI TIẾT</a>
						</div>
					</div>
				</div>
			</section>
			<section class="user-box">
				<div class="container">
					<div-- class="row">
						<div class="col-xl-12 slick-user-info">
                            @if($reviews)
                            @foreach($reviews as $review)
							<div>
								<div class="description-user-info">
									{!!$review['content']!!}
								</div>
								<div class="user-avarta-name">
									<img src="{{ Storage::url($review['image']) }}">
									<div class="h4">{{$review['name']}}</div>
									<p>{{$review['title']}}</p>
								</div>
							</div>
                            @endforeach
                            @endif

						</div>
						<!--div class="col-xl-12">
							<div class="register-user">
								<div class="h3">ĐĂNG KÝ THÀNH VIÊN</div>
								<div class="form-group">
									<input type="text" class="form-control" placeholder="Họ và tên">
								</div>
								<div class="form-group">
									<input type="text" class="form-control" placeholder="Số điện thoại">
								</div>
								<div class="form-group">
									<input type="text" class="form-control" placeholder="Email">
								</div>
								<div class="form-group text-center pt-3">
									<button class="btn transition btn-more">ĐĂNG KÝ</button>
								</div>
							</div>
						</div-->
					</div>
				</div>
			</section>
            
			<section class="comment-box">
				<div class="container">
                    <div class="customer-fb slider">
                        <div>
                            <h3><img src="/images/landing/1.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/2.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/3.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/4.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/5.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/6.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/7.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/8.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/9.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/10.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/11.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/12.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/13.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/14.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/15.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/16.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/17.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/18.jpg" /></h3>
                        </div>
                        <div>
                            <h3><img src="/images/landing/19.jpg" /></h3>
                        </div>
                        
                    </div>
				</div>
            </section>

@endsection

@section('styles')

<link rel="stylesheet" href="{{ asset('owl-carousel/assets/owl.carousel.min.css')}}"/>
<link rel="stylesheet" href="{{ asset('owl-carousel/assets/owl.theme.default.min.css')}}"/>

@stop

@push('scripts')

<script src="{{ asset('owl-carousel/owl.carousel.min.js')}}"></script>
<script>

function getPackages(){
    var packageType = $('#packageType').val();
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    if(packageType === 'single'){
        $.ajax({
            method: "POST",
            url: "{{ route('api.package.getComboPackages') }}",
            data: {_token: CSRF_TOKEN}
        })
        .done(function( data ) {
            $('#resultPackage').html(data);
            $('#packageType').val('combo');
        });
    }else{
        $.ajax({
            method: "POST",
            url: "{{ route('api.course.getCourses') }}",
            data: {_token: CSRF_TOKEN}
        })
        .done(function( data ) {
            $('#resultPackage').html(data);
            $('#packageType').val('single');
        });
    }
}

$('.banner').slick({
    autoplay: true,
    autoplaySpeed: 5000
});

$('.customer-fb').slick({
    autoplay:true,
    centerMode: true,
    centerPadding: '60px',
    slidesToShow: 3,
    responsive: [
        {
        breakpoint: 768,
        settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 3
            }
        },
        {
        breakpoint: 480,
        settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 1
            }
        }
    ]
});

$(".owl-3-items").owlCarousel({
    nav:false,
    loop:false,
    dots: true,
    margin:30,
    responsive:{
        0:{
            items:1
        },
        768:{
            items:2
        },
        1024:{
            items:3
        }
    }
});

$('.slick-user-info').slick({
    dots: true,
    infinite: false,
    speed: 300,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 5000,
    arrows: true,
});
</script>

@endpush
