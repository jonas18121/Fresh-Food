<?php

namespace App\Entity;

class SearchProductData {

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var Category[]
     */
    public $categories = [];

    /**
     * @var Emplacement[]
     */
    public $emplacements = [];
}