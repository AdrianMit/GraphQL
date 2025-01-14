<?php

namespace Dreamlabs\GraphQL\Parser;


class Location
{

    /**
     * @param int $line
     * @param int $column
     */
    public function __construct(private $line, private $column)
    {
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }


    public function toArray()
    {
        return [
            'line'   => $this->getLine(),
            'column' => $this->getColumn()
        ];
    }

}
