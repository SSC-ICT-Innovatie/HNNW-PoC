@extends("layouts.app")

@section("breadcrumbs")
    {!! Breadcrumbs::render("home") !!}
@stop

@section("content")
    <div class="content-section">

        <h1 class="page-title centered">Het Nieuwe Nieuwe Werken</h1>
        
        @include("partials.feedback")

        

    </div>
@stop