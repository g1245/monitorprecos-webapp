@extends('layouts.app')

@section('title', 'Encontrando seu grupo… - Monitor de Preços')
@section('description', 'Aguarde, estamos encaminhando você para o grupo do WhatsApp do Monitor de Preços.')

@push('tracking_events')
    <x-tracking-event name="Lead" />
    <x-tracking-event name="ViewContent" :data="['content_name' => 'WhatsApp Grupo - Redirecionamento', 'content_category' => 'grupo']" />
@endpush

@section('content')

@php $whatsappGroupLink = 'https://chat.whatsapp.com/Fs0jA2Xbvs0HwJwOQErZtm'; @endphp

<div
    x-data="{
        step: 0,
        steps: [
            'Verificando grupos disponíveis…',
            'Encontramos um grupo para você!',
            'Encaminhando para o WhatsApp…',
        ],
        destination: '{{ $whatsappGroupLink }}',
        init() {
            this.advance();
        },
        advance() {
            const delays = [900, 1600, 1200];
            const run = (index) => {
                if (index >= this.steps.length) {
                    window.location.href = this.destination;
                    return;
                }
                this.step = index;
                setTimeout(() => run(index + 1), delays[index]);
            };
            run(0);
        }
    }"
    class="min-h-screen bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center px-4"
    aria-live="polite"
    aria-label="Encaminhando para o grupo do WhatsApp"
>
    <div class="text-center max-w-sm w-full">

        {{-- WhatsApp icon with pulse ring --}}
        <div class="relative inline-flex items-center justify-center mb-8">
            <span class="absolute inline-flex w-24 h-24 rounded-full bg-white/30 animate-ping" aria-hidden="true"></span>
            <div class="relative flex items-center justify-center w-20 h-20 rounded-full bg-white/20 ring-4 ring-white/40">
                <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                    <path d="M12 0C5.373 0 0 5.373 0 12c0 2.125.558 4.118 1.532 5.845L.057 23.882a.5.5 0 0 0 .612.612l6.037-1.475A11.942 11.942 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.808 9.808 0 0 1-5.032-1.385l-.36-.214-3.733.912.93-3.733-.233-.373A9.808 9.808 0 0 1 2.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                </svg>
            </div>
        </div>

        {{-- Dynamic status message --}}
        <div class="relative mb-6 h-16 flex items-center justify-center">
            <template x-for="(text, index) in steps" :key="index">
                <p
                    x-show="step === index"
                    x-transition:enter="transition-opacity ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 flex items-center justify-center text-xl md:text-2xl font-bold text-white"
                    x-text="text"
                ></p>
            </template>
        </div>

        {{-- Progress dots --}}
        <div class="flex items-center justify-center gap-2 mb-8" aria-hidden="true">
            <template x-for="(_, index) in steps" :key="index">
                <span
                    class="block w-2.5 h-2.5 rounded-full transition-all duration-500"
                    :class="step >= index ? 'bg-white scale-125' : 'bg-white/30'"
                ></span>
            </template>
        </div>

        {{-- Fallback manual link --}}
        <p class="text-sm text-white/70">
            Não foi redirecionado?
            <a
                href="{{ $whatsappGroupLink }}"
                target="_blank"
                rel="noopener noreferrer"
                class="underline text-white font-medium hover:text-white/90"
            >Clique aqui</a>
        </p>

    </div>
</div>

@endsection
