@extends('layouts.app')

{{--
    One shared template for the footer's legal pages (Privacy Policy, Terms
    and Conditions, Cookie Policy) — each route just passes a $title, see
    routes/web.php. Content is a placeholder until real copy is written in.
--}}

@section('content')

    <x-hero
        :lines="[$title]"
        :breadcrumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => $title, 'url' => url()->current()],
        ]" />

    <div class="mil-p-0-15" id="scroll">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mil-mb-15 mil-up">
                    <p class="mil-fs-18 mil-lh-160">Content coming soon.</p>
                </div>
            </div>
        </div>
    </div>

@endsection
