<?php

namespace App\Services;

class SeoService
{
    protected array $data = [];

    public function __construct()
    {
        // Safe defaults for every page — override per route in your controller
        $this->data = [
            'title'       => 'Nomad Tax Calculator — Free Tax Tool for Digital Nomads',
            'description' => 'Calculate your taxes as a digital nomad in seconds. Compare tax rates across 50+ countries. Free, instant, no signup required.',
            'canonical'   => url()->current(),
            'og_image'    => asset('images/og-default.png'),
            'og_type'     => 'website',
            'robots'      => 'index, follow',
            'schema'      => null,
        ];
    }

    /**
     * Merge page-specific SEO data over defaults.
     */
    public function set(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Mark a page as noindex / nofollow (auth pages, shared tokens, etc.).
     */
    public function noIndex(): self
    {
        $this->data['robots'] = 'noindex, nofollow';
        return $this;
    }

    /**
     * Return the resolved SEO array to pass as an Inertia prop.
     */
    public function get(): array
    {
        return $this->data;
    }
}
