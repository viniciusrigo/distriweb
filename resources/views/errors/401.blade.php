@extends('errors::minimal')

@section('title', __('Não autorizado'))
@section('code', '401')
@section('message', __('Você não tem permissão para realizar esta ação'))
