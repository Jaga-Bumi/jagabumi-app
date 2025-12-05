@extends('layouts.main')

@section('title', 'JagaBumi.id - Platform Aksi Lingkungan #1 Indonesia')

@section('content')
    @include('components.home.hero')
    @include('components.home.active-quest')
    @include('components.home.hot-articles')
    @include('components.home.about')
    @include('components.home.faq')
@endsection
