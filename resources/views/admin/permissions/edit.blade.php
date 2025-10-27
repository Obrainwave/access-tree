@extends('accesstree::admin.layouts.resource-form')

@section('title', 'Edit Permission')

@php
    $action = route('accesstree.admin.permissions.update', $item);
    $title = 'Edit Permission';
@endphp
