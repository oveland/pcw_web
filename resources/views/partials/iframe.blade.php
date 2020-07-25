@extends('layout')

@section('stylesheets')
    <style>
        .page-content{
            padding: 0 !important;
        }
    </style>
@endsection

@section('content')
    <iframe class="" src="{{ $link }}" width="100%" height="1100px"></iframe>
@endsection