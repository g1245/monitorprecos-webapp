<?php

/**
 * Configuração dos grupos de WhatsApp/Telegram por nicho.
 *
 * Cada entrada define uma landing page acessível em /grupo/{slug}.
 * Preencha o campo `whatsapp_link` com o link real do grupo.
 */
return [

    'corredores' => [
        'name'           => 'Corredores',
        'emoji'          => '🏃🏃‍♂️',
        'headline'       => 'Promoções exclusivas para corredores!',
        'description'    => 'Promoções e cupons exclusivos de tênis, vestuário, GPS, suplementos e acessórios para corrida. Economize até 70% nas maiores lojas do Brasil.',
        'whatsapp_link'  => 'https://chat.whatsapp.com/Fs0jA2Xbvs0HwJwOQErZtm',
    ],

    'moda' => [
        'name'           => 'Moda',
        'emoji'          => '👗',
        'headline'       => 'As melhores promoções de moda no seu celular!',
        'description'    => 'Roupas, acessórios e tendências das principais lojas do Brasil com descontos de até 70%. Fique por dentro das ofertas do dia.',
        'benefits'       => [
            ['title' => 'Roupas com descontos reais', 'description' => 'Nossa IA monitora os preços e só avisa quando o desconto for de verdade.'],
            ['title' => 'Todas as ocasiões', 'description' => 'Moda casual, trabalho, festa e esportes — cobrimos todas as categorias.'],
            ['title' => 'Lojas verificadas', 'description' => 'Apenas lojas reconhecidas e de confiança para você comprar com segurança.'],
        ],
        'whatsapp_link'  => '#',
    ],

    'calcados' => [
        'name'           => 'Calçados',
        'emoji'          => '👟',
        'headline'       => 'Promoções de calçados para todos os estilos!',
        'description'    => 'Tênis, sapatênis, botas, sandálias e muito mais com os melhores preços das lojas virtuais de todo o Brasil.',
        'benefits'       => [
            ['title' => 'Monitoramento de preços', 'description' => 'Acompanhamos o histórico de preços e avisamos no mínimo histórico.'],
            ['title' => 'Todas as marcas', 'description' => 'Nike, Adidas, Vans, Arezzo, Democrata e muito mais nas melhores condições.'],
            ['title' => 'Ofertas em tempo real', 'description' => 'Assim que o preço cair, você recebe a oferta antes de todo mundo.'],
        ],
        'whatsapp_link'  => '#',
    ],

    'tenis' => [
        'name'           => 'Tênis',
        'emoji'          => '👟',
        'headline'       => 'Os melhores tênis com o menor preço!',
        'description'    => 'Nike, Adidas, New Balance, ASICS e muito mais. Nossa IA monitora os preços e te avisa quando bater o menor valor já registrado.',
        'benefits'       => [
            ['title' => 'Menor preço histórico', 'description' => 'Avisamos quando o tênis que você quer atingir o menor preço já registrado.'],
            ['title' => 'Todas as categorias', 'description' => 'Corrida, casual, skate, basquete, futsal — qualquer estilo, qualquer preço.'],
            ['title' => 'Sem spam, só ofertas reais', 'description' => 'Só enviamos quando a promoção for genuína, com base no histórico de preços.'],
        ],
        'whatsapp_link'  => '#',
    ],

];
