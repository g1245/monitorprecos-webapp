@extends('layouts.landing')
@section('title', $group['headline'] . ' - Monitor de Preços')
@section('description', $group['description'])
@push('meta')
    <meta name="robots" content="index, follow">
@endpush
@push('tracking_events')
    <x-tracking-event name="ViewContent" :data="['content_name' => 'Grupo ' . $group['name'], 'content_category' => 'grupo-' . $niche]" />
@endpush
@section('content')
    <style>
        @keyframes bounce-gentle {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-6px); }
        }
        .btn-bounce {
            animation: bounce-gentle 1.8s ease-in-out infinite;
        }
        .btn-bounce:hover {
            animation: none;
        }
    </style>

    <section class="relative overflow-hidden bg-gradient-to-br from-primary to-[#0143C9] min-h-screen flex flex-col">
        <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
            <svg class="w-full h-full" viewBox="0 0 800 500" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                <circle cx="720" cy="-60" r="320" fill="white"/>
                <circle cx="80" cy="560" r="260" fill="white"/>
            </svg>
        </div>

        <div class="relative flex-1 container mx-auto px-4 py-16 flex flex-col items-center justify-center text-center">

            {{-- Logo --}}
            <div class="relative inline-block mb-6">
                <img
                    src="{{ Vite::asset('resources/images/logo-small.png') }}"
                    alt="Monitor de Preços"
                    class="h-20 w-20 rounded-full ring-4 ring-white/30 object-contain bg-white p-1 shadow-xl"
                >
                {{-- Online badge --}}
                <span class="absolute bottom-1 right-1 flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-green-400 ring-2 ring-white/30"></span>
                </span>
            </div>

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 bg-white/15 text-white text-sm font-semibold px-4 py-1.5 rounded-full ring-1 ring-white/30 mb-5">
                <span>100% GRATUITO &bull; Milhares de membros</span>
            </div>

            {{-- Headline --}}
            <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight max-w-2xl">
                {{ $group['headline'] }} {{ $group['emoji'] }}
            </h1>

            <p class="mt-5 text-lg md:text-xl text-white/90 max-w-xl leading-relaxed">
                {!! nl2br(e($group['description'])) !!}
            </p>

            {{-- CTA WhatsApp --}}
            <div class="mt-9">
                <a
                    href="{{ $group['whatsapp_link'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    onclick="if(typeof fbq==='function'){fbq('track','Lead',{content_name:'Grupo {{ $group['name'] }}',content_category:'grupo-{{ $niche }}'});}"
                    class="btn-bounce inline-flex items-center justify-center gap-3 bg-[#1a9e4f] text-white font-bold text-lg px-8 py-4 rounded-2xl shadow-xl hover:brightness-110 active:scale-95 transition-[filter,transform] duration-200"
                    aria-label="Entrar no grupo do WhatsApp de {{ $group['name'] }}"
                >
                    <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.558 4.118 1.532 5.845L.057 23.882a.5.5 0 0 0 .612.612l6.037-1.475A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 0 1-5.032-1.385l-.36-.214-3.733.912.93-3.733-.233-.373A9.808 9.808 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                    </svg>
                    Entrar no grupo do WhatsApp
                </a>
                <p class="mt-3 text-sm text-white/70">Toque no botão para entrar gratuitamente</p>
            </div>
        </div>
    </section>
@endsection
