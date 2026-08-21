{{-- Sticky right-hand buttons plus the "order a call" flyout. --}}
<div class="mil-right-buttons-frame">
    <div class="mil-order-call-window">
        <div class="mil-ocw-content">
            <div class="row mil-aic">
                <div class="col-lg-7">
                    <form action="{{ route('callback') }}" method="POST">
                        @csrf
                        <x-honeypot suffix="-2" />
                        <h4 class="mil-fs-28 mil-mb-30">Rug, sofa or armchair? <br>We'll call you back today</h4>
                        <div class="mil-input-frame mil-mb-15">
                            <input type="text" id="user-name-2" name="name" placeholder="Name" class="mil-br-md mil-bg-m-3" autocomplete="name" value="{{ old('name') }}">
                            <i class="far fa-user mil-a-2"></i>
                        </div>
                        <div class="mil-input-frame mil-mb-30">
                            <input type="tel" id="user-phone-2" name="phone" class="mil-phone-input mil-br-md mil-bg-m-3" placeholder="+1 (___) ___-____" autocomplete="tel" value="{{ old('phone') }}">
                            <i class="far fa-mobile mil-a-2"></i>
                        </div>
                        <button class="mil-btn mil-bg-a-1 mil-icon-btn mil-br-xl mil-hover-bri-105 mil-hover-scale" type="submit">Order a call<i class="far fa-arrow-right mil-bg-m-1 mil-a-1"></i></button>
                    </form>
                </div>
                <div class="col-lg-5">
                    <img src="{{ asset('img/ui/cc.jpg') }}" alt="callcenter" class="mil-ocw-img mil-br-sm">
                </div>
            </div>
        </div>
    </div>
    <div class="mil-right-buttons">
        <a href="#top" class="mil-side-btn mil-back-to-top mil-hover-bri-120">
            <i class="far fa-arrow-up mil-a-1"></i>
        </a>
        <button class="mil-side-btn mil-open-window mil-hover-bri-120"></button>
    </div>
</div>
