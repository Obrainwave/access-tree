@extends('accesstree::admin.layouts.resource-form')

@section('title', 'Create Permission')

@php
    $action = route('accesstree.admin.permissions.store');
    $title = 'Create Permission';
@endphp
