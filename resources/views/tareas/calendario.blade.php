@extends('layouts.app')

    @section('styles')
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4">
    <h2>Calendario de Subtareas</h2>
    <div id="calendar"></div>
</div>
@endsection

@section('scripts')
    <!-- FullCalendar JS + locales -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <!-- Tu inicializador -->
    <script src="{{ asset('js/fullcalendar-init.js') }}"></script>
@endsection
