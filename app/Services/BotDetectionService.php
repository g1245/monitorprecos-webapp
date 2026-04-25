<?php

namespace App\Services;

class BotDetectionService
{
    /**
     * Generic substrings that identify the vast majority of crawlers.
     * Checked via str_contains on the lowercased user-agent string.
     *
     * @var array<int, string>
     */
    private const GENERIC_SIGNATURES = [
        'bot',
        'crawler',
        'spider',
        'scraper',
        'indexer',
        'fetcher',
        'slurp',
        'preview',
    ];

    /**
     * Specific user-agent identifiers that do NOT contain any of the generic
     * signatures above but still represent non-human traffic.
     *
     * @var array<int, string>
     */
    private const SPECIFIC_SIGNATURES = [
        // AI / LLM crawlers
        'anthropic-ai',
        'claude-user',
        'claude-web',
        'claude-code',
        'chatgpt-operator',
        'chatgpt-user',
        'oai-searchbot',
        'perplexity-user',
        'mistralai-user',
        'cohere-ai',
        'cohere-training-data-crawler',
        'google-extended',
        'googleother',
        'google-cloudvertexbot',
        'google-notebooklm',
        'google-read-aloud',
        'google-agent',
        'bard-ai',
        'gemini-ai',
        'gemini-deep-research',
        'gptbot',
        'grok',
        'ai2bot',
        'ccbot',
        'bytespider',
        'diffbot',
        'omgili',
        'iaskspider',
        'timpibot',
        'webzio-extended',
        'big-sur-ai',
        'digitaloceangenai-crawler',
        'scrapy',
        'meta-externalagent',
        'meta-externalfetcher',
        'facebookexternalhit',

        // Traditional search engines (identifiers without "bot"/"spider"/etc.)
        'mediapartners-google',
        'adsbot-google',
        'msnbot',
        'sogou',
        'yeti',
        'qwantify',
        'exabot',
        'baiduspider',

        // Social media link-preview agents
        'whatsapp',
        'pinterest',
        'discordbot',
        'slackbot',
        'telegrambot',
        'linkedinbot',

        // SEO tools / commercial scrapers
        'screamingfrogseospider',
        'rogerbot',
        'serpstatbot',
        'blexbot',
        'seokicks',
    ];

    /**
     * Determine whether the given user-agent string belongs to a bot,
     * crawler, or other non-human client.
     */
    public function isBot(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return true;
        }

        $lowerUserAgent = strtolower($userAgent);

        foreach (self::GENERIC_SIGNATURES as $signature) {
            if (str_contains($lowerUserAgent, $signature)) {
                return true;
            }
        }

        foreach (self::SPECIFIC_SIGNATURES as $signature) {
            if (str_contains($lowerUserAgent, $signature)) {
                return true;
            }
        }

        return false;
    }
}
