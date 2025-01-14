<?php

namespace Examples\StarWars;


class TestDataProvider
{
    private static array $ships = [
        'xwing' => ['id' => 1, 'name' => 'X-Wing'],
        'ywing' => ['id' => 2, 'name' => 'Y-Wing'],
        'awing' => ['id' => 3, 'name' => 'A-Wing'],
        
        'falcon'         => ['id' => 4, 'name' => 'Millenium Falcon'],
        'homeOne'        => ['id' => 5, 'name' => 'Home One'],
        'tieFighter'     => ['id' => 6, 'name' => 'TIE Fighter'],
        'tieInterceptor' => ['id' => 7, 'name' => 'TIE Interceptor'],
        'executor'       => ['id' => 8, 'name' => 'Executor'],
    ];
    
    private static array $factions = [
        '1' => [
            'id'    => 1,
            'name'  => 'Alliance to Restore the Republic',
            'ships' => [1, 2, 3, 4, 5],
        ],
        '2' => [
            'id'    => 2,
            'name'  => 'Galactic Empire',
            'ships' => [6, 7, 8],
        ],
    ];
    
    private static int $nextShipId = 9;
    
    public static function getByNames($names): array
    {
        $result = [];
        
        foreach ($names as $name) {
            if ($name == 'rebels') {
                $result[] = self::$factions[1];
            } elseif ($name == 'empire') {
                $result[] = self::$factions[2];
            } else {
                $result[] = null;
            }
        }
        
        return $result;
    }
    
    public static function createShip($name, $factionId): array
    {
        $newShip = [
            'id'   => self::$nextShipId++,
            'name' => $name,
        ];
        
        self::$ships[$newShip['id']]           = $newShip;
        self::$factions[$factionId]['ships'][] = $newShip['id'];
        
        return $newShip;
    }
    
    public static function getFactions(): array
    {
        return self::$factions;
    }
    
    public static function getShip($id)
    {
        foreach (self::$ships as $ship) {
            if ($ship['id'] == $id) {
                return $ship;
            }
        }
        
        return null;
    }
    
    public static function getShips(): array
    {
        return self::$ships;
    }
    
    public static function getFaction($id)
    {
        if ($id == 'rebels') {
            return self::$factions[1];
        } elseif ($id == 'empire') {
            return self::$factions[2];
        }
        if (array_key_exists($id, self::$factions)) {
            return self::$factions[$id];
        }
        
        return null;
    }
}
