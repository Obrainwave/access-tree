@extends('accesstree::admin.layouts.resource-form')

@section('title', 'Edit Role')

@php
    $action = route('accesstree.admin.roles.update', $item);
    $title = 'Edit Role';
@endphp
