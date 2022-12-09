<?php

namespace GroupThr3e\AJExchange\Models;

class HomepageData
{
    /** @var Listing[] $recentSales */
    public array $recentSales;

    /** @var Listing[] $recentExchanges  */
    public array $recentExchanges;

    /** @var Listing[] $recentGiveaways */
    public array $recentGiveaways;

    public array $tags;

    public function __construct(array $recentSales, array $recentExchanges, array $recentGiveaways, array $tags)
    {
        $this->recentSales = $recentSales;
        $this->recentExchanges = $recentExchanges;
        $this->recentGiveaways = $recentGiveaways;
        $this->tags = $tags;
    }


}