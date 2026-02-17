<?php

declare(strict_types=1);

function baseIdsByCategory(): array
{
    return [
        'action' => ['6ZfuNTqbHE8', 'zSWdZVtXT7E', 'TcMBFSGVi1c', 's7EdQ4FqbhY', 'vKQi3bBA1y8'],
        'anime' => ['6ohYYtxfDCg', 'MGRm4IzK1SQ', 'h7M6jQ0Qw9s', 'YB1Gvd0n7qA', 'Bw-5Lka7gPE'],
        'comedy' => ['n4t9h6Y8Y8A', 'FWSxSQsspiQ', 'k4M53xndqiU', 'QdBZY2fkU-0', 'fLexgOxsZu0'],
    ];
}

function fullCatalog(): array
{
    $idsByCategory = baseIdsByCategory();
    $catalog = [];

    foreach ($idsByCategory as $category => $ids) {
        $items = [];
        for ($i = 1; $i <= 100; $i++) {
            $videoId = $ids[($i - 1) % count($ids)];
            $items[] = [
                'title' => ucfirst($category) . ' Video ' . $i,
                'videoId' => $videoId,
                'category' => $category,
            ];
        }
        $catalog[$category] = $items;
    }

    return $catalog;
}
