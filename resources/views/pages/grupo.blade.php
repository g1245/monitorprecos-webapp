@extends('layouts.app')

@section('title', 'Grupos de Descontos no WhatsApp - Monitor de Preços')
@section('description', 'Entre nos grupos do Monitor de Preços no WhatsApp e receba promoções selecionadas de grandes lojas do Brasil direto no seu celular.')

@push('tracking_events')
    <x-tracking-event name="ViewContent" :data="['content_name' => 'WhatsApp Grupo', 'content_category' => 'grupo']" />
@endpush

@section('content')

@php $whatsappGroupLink = route('pages.grupo.redirect'); @endphp

{{-- ====================================================
     HERO
     ==================================================== --}}
<section class="relative overflow-hidden bg-gradient-to-br from-green-500 to-teal-600">
    <div class="absolute inset-0 opacity-10 pointer-events-none" aria-hidden="true">
        <svg class="w-full h-full" viewBox="0 0 800 600" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <circle cx="700" cy="-50" r="300" fill="white"/>
            <circle cx="100" cy="600" r="250" fill="white"/>
        </svg>
    </div>

    <div class="relative container mx-auto px-4 py-16 md:py-24 text-center">
        {{-- WhatsApp icon --}}
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/20 mb-6 ring-4 ring-white/30">
            <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.558 4.118 1.532 5.845L.057 23.882a.5.5 0 0 0 .612.612l6.037-1.475A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 0 1-5.032-1.385l-.36-.214-3.733.912.93-3.733-.233-.373A9.808 9.808 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
            </svg>
        </div>

        {{-- Headline --}}
        <h1 class="text-3xl md:text-5xl font-extrabold text-white leading-tight max-w-3xl mx-auto">
            Receba ofertas e descontos<br class="hidden md:block"> no WhatsApp antes de comprar
        </h1>

        <p class="mt-5 text-lg md:text-xl text-white/90 max-w-xl mx-auto leading-relaxed">
            Entre nos grupos do Monitor de Preços.<br>
            Usamos <strong>inteligência artificial</strong> para monitorar preços e selecionar as melhores promoções de <strong>grandes lojas do Brasil</strong>, direto no seu celular.
        </p>

        {{-- Primary CTA --}}
        <div class="mt-8">
            <a
                href="{{ $whatsappGroupLink }}"
                class="inline-flex items-center gap-3 bg-white text-green-600 font-bold text-lg px-8 py-4 rounded-2xl shadow-xl hover:shadow-2xl hover:bg-green-50 transition-all duration-200 active:scale-95"
                aria-label="Entrar no grupo do WhatsApp do Monitor de Preços"
            >
                <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.558 4.118 1.532 5.845L.057 23.882a.5.5 0 0 0 .612.612l6.037-1.475A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 0 1-5.032-1.385l-.36-.214-3.733.912.93-3.733-.233-.373A9.808 9.808 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                </svg>
                Entrar no grupo do WhatsApp
            </a>
            <p class="mt-3 text-sm text-white/75">Toque no botão para entrar no grupo</p>
        </div>

        {{-- Trust badge --}}
        <div class="mt-8 inline-flex items-center gap-2 bg-white/15 text-white text-sm font-medium px-4 py-2 rounded-full ring-1 ring-white/30">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 1a.75.75 0 0 1 .673.418l1.882 3.815 4.21.612a.75.75 0 0 1 .416 1.279l-3.046 2.97.719 4.192a.75.75 0 0 1-1.088.791L10 13.347l-3.766 1.98a.75.75 0 0 1-1.088-.79l.72-4.194L2.82 7.125a.75.75 0 0 1 .416-1.28l4.21-.611L9.327 1.418A.75.75 0 0 1 10 1z" clip-rule="evenodd"/>
            </svg>
            Ofertas de grandes lojas do Brasil
        </div>
    </div>
</section>

{{-- ====================================================
     BENEFÍCIOS
     ==================================================== --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-12">
            Por que entrar no grupo?
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
            {{-- Benefício 1 --}}
            <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-gray-50 hover:bg-green-50 transition-colors duration-200">
                <div class="flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-4">
                    <svg class="w-7 h-7 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                        <polyline points="13 17 18 12 13 7"/>
                        <polyline points="6 17 11 12 6 7"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Ofertas encontradas mais rápido</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Nossa inteligência artificial monitora os preços em tempo real e identifica as melhores promoções antes de todo mundo. Sem precisar procurar.</p>
            </div>

            {{-- Benefício 2 --}}
            <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-gray-50 hover:bg-green-50 transition-colors duration-200">
                <div class="flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-4">
                    <svg class="w-7 h-7 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Links de lojas confiáveis</h3>
                <p class="text-gray-600 text-sm leading-relaxed">A IA analisa e filtra apenas lojas virtuais verificadas e reconhecidas no Brasil. Compre com segurança e tranquilidade.</p>
            </div>

            {{-- Benefício 3 --}}
            <div class="flex flex-col items-center text-center p-6 rounded-2xl bg-gray-50 hover:bg-green-50 transition-colors duration-200">
                <div class="flex items-center justify-center w-14 h-14 rounded-full bg-green-100 mb-4">
                    <svg class="w-7 h-7 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Avisos direto no celular</h3>
                <p class="text-gray-600 text-sm leading-relaxed">Quando a IA detecta uma variação de preço relevante, você recebe o aviso no WhatsApp na hora. Sem precisar abrir nenhum app.</p>
            </div>
        </div>
    </div>
</section>

{{-- ====================================================
     COMO FUNCIONA
     ==================================================== --}}
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-12">
            Como funciona?
        </h2>

        <div class="relative max-w-3xl mx-auto">
            {{-- Connector line (desktop only) --}}
            <div class="hidden md:block absolute top-8 left-1/2 -translate-x-1/2 w-2/3 h-0.5 bg-green-200" aria-hidden="true"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                {{-- Passo 1 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="relative z-10 flex items-center justify-center w-16 h-16 rounded-full bg-green-500 text-white text-2xl font-extrabold shadow-lg mb-4">
                        1
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Entre no grupo</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Clique no botão e acesse o grupo do WhatsApp em segundos. É gratuito e sem cadastro.</p>
                </div>

                {{-- Passo 2 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="relative z-10 flex items-center justify-center w-16 h-16 rounded-full bg-green-500 text-white text-2xl font-extrabold shadow-lg mb-4">
                        2
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Receba as melhores ofertas</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Nossa IA monitora preços continuamente e seleciona apenas as promoções que realmente valem a pena, com links diretos para as lojas.</p>
                </div>

                {{-- Passo 3 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="relative z-10 flex items-center justify-center w-16 h-16 rounded-full bg-green-500 text-white text-2xl font-extrabold shadow-lg mb-4">
                        3
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Clique e aproveite</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">Viu uma oferta boa? Clique no link, compre na loja e economize. Simples assim.</p>
                </div>
            </div>
        </div>

        {{-- CTA no meio da página --}}
        <div class="mt-12 text-center">
            <a
                href="{{ $whatsappGroupLink }}"
                class="inline-flex items-center gap-3 bg-green-500 text-white font-bold text-base px-7 py-3.5 rounded-xl shadow-md hover:bg-green-600 transition-colors duration-200 active:scale-95"
                aria-label="Entrar no grupo do WhatsApp do Monitor de Preços"
            >
                <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.558 4.118 1.532 5.845L.057 23.882a.5.5 0 0 0 .612.612l6.037-1.475A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 0 1-5.032-1.385l-.36-.214-3.733.912.93-3.733-.233-.373A9.808 9.808 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                </svg>
                Quero entrar no grupo
            </a>
        </div>
    </div>
</section>

{{-- ====================================================
     FAIXA CTA FINAL
     ==================================================== --}}
<section class="bg-gradient-to-r from-green-600 to-teal-600 py-14">
    <div class="container mx-auto px-4 text-center">
        <p class="text-sm font-semibold uppercase tracking-widest text-white/70 mb-3">Vagas limitadas</p>
        <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-3">
            Entre agora e não perca nenhuma oferta
        </h2>
        <p class="text-white/80 mb-8 max-w-md mx-auto">
            As melhores promoções têm tempo limitado. Esteja no grupo e seja avisado em primeira mão.
        </p>
        <a
            href="{{ $whatsappGroupLink }}"
            class="inline-flex items-center gap-3 bg-white text-green-600 font-bold text-lg px-8 py-4 rounded-2xl shadow-xl hover:shadow-2xl hover:bg-green-50 transition-all duration-200 active:scale-95"
            aria-label="Entrar no grupo do WhatsApp do Monitor de Preços"
        >
            <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.558 4.118 1.532 5.845L.057 23.882a.5.5 0 0 0 .612.612l6.037-1.475A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 0 1-5.032-1.385l-.36-.214-3.733.912.93-3.733-.233-.373A9.808 9.808 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
            </svg>
            Entrar no grupo do WhatsApp
        </a>
        <p class="mt-4 text-sm text-white/60">Toque no botão para entrar no grupo</p>
    </div>
</section>

{{-- ====================================================
     CTA FIXO MOBILE
     Aparece após scroll de 200px. Oculto em telas md+.
     ==================================================== --}}
<div
    x-data="{ show: false }"
    x-init="window.addEventListener('scroll', () => { show = window.scrollY > 200 })"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-0 inset-x-0 z-50 p-4 bg-white/95 backdrop-blur border-t border-gray-200 shadow-2xl md:hidden"
    aria-label="CTA rápido para entrar no grupo do WhatsApp"
    style="display: none;"
>
    <a
        href="{{ $whatsappGroupLink }}"
        class="flex items-center justify-center gap-3 w-full bg-green-500 text-white font-bold text-base py-3.5 rounded-xl shadow-md hover:bg-green-600 transition-colors duration-200 active:scale-95"
        aria-label="Entrar no grupo do WhatsApp"
    >
        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.558 4.118 1.532 5.845L.057 23.882a.5.5 0 0 0 .612.612l6.037-1.475A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 0 1-5.032-1.385l-.36-.214-3.733.912.93-3.733-.233-.373A9.808 9.808 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
        </svg>
        Entrar no grupo do WhatsApp
    </a>
</div>

@endsection
