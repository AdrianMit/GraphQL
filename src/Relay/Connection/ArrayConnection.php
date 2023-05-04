<?php

namespace Dreamlabs\GraphQL\Relay\Connection;


class ArrayConnection
{

    public const PREFIX = 'arrayconnection:';

    public static function cursorForObjectInConnection($data, $object)
    {
        if (!is_array($data)) return null;

        $index = array_search($object, $data);
        return $index === false ? null : (string) self::keyToCursor($index);
    }

    /**
     * @param $offset int
     * @deprecated
     *   Use keyToCursor instead.
     */
    public static function offsetToCursor($offset): string
    {
      return self::keyToCursor($offset);
    }

    public static function keyToCursor($key): string
    {
      return base64_encode(self::PREFIX . $key);
    }

    /**
     * @param $cursor string
     *
     * @deprecated Use cursorToKey instead.
     */
    public static function cursorToOffset($cursor): ?int
    {
        return self::cursorToKey($cursor);
    }

  /**
     * Converts a cursor to its array key.
     *
     * @param $cursor
     */
    public static function cursorToKey($cursor): ?string {
      if ($decoded = base64_decode($cursor)) {
        return substr($decoded, strlen(self::PREFIX));
      }
      return null;
    }

  /**
     * Converts a cursor to an array offset.
     *
     * @param $cursor
     *   The cursor string.
     * @param $default
     *   The default value, in case the cursor is not given.
     * @param array $array
     *   The array to use in counting the offset. If empty, assumed to be an indexed array.
     */
    public static function cursorToOffsetWithDefault($cursor, $default, $array = []): ?int
    {
        if (!is_string($cursor)) {
            return $default;
        }

        $key = self::cursorToKey($cursor);
        if (empty($array)) {
          $offset = $key;
        }
        else {
          $offset = array_search($key, array_keys($array));
        }

        return is_null($offset) ? $default : (int) $offset;
    }

    public static function connectionFromArray(array $data, array $args = [])
    {
        return self::connectionFromArraySlice($data, $args, 0, count($data));
    }

    public static function connectionFromArraySlice(array $data, array $args, $sliceStart, $arrayLength)
    {
        $after  = $args['after'] ?? null;
        $before = $args['before'] ?? null;
        $first  = $args['first'] ?? null;
        $last   = $args['last'] ?? null;

        $sliceEnd = $sliceStart + count($data);

        $beforeOffset = ArrayConnection::cursorToOffsetWithDefault($before, $arrayLength, $data);
        $afterOffset  = ArrayConnection::cursorToOffsetWithDefault($after, -1, $data);

        $startOffset = max($sliceStart - 1, $afterOffset, -1) + 1;
        $endOffset   = min($sliceEnd, $beforeOffset, $arrayLength);

        if ($first) {
            $endOffset = min($endOffset, $startOffset + $first);
        }

        if ($last) {
            $startOffset = max($startOffset, $endOffset - $last);
        }

        $arraySliceStart    = max($startOffset - $sliceStart, 0);
        $arraySliceEnd      = count($data) - ($sliceEnd - $endOffset) - $arraySliceStart;

        $slice = array_slice($data, $arraySliceStart, $arraySliceEnd, true);
        $edges = array_map(['self', 'edgeForObjectWithIndex'], $slice, array_keys($slice));

        $firstEdge  = array_key_exists(0, $edges) ? $edges[0] : null;
        $lastEdge   = count($edges) > 0 ? $edges[count($edges) - 1] : null;
        $lowerBound = $after ? $afterOffset + 1 : 0;
        $upperBound = $before ? $beforeOffset : $arrayLength;

        return [
            'totalCount' => $arrayLength,
            'edges'      => $edges,
            'pageInfo'   => [
                'startCursor'     => $firstEdge ? $firstEdge['cursor'] : null,
                'endCursor'       => $lastEdge ? $lastEdge['cursor'] : null,
                'hasPreviousPage' => $last ? $startOffset > $lowerBound : false,
                'hasNextPage'     => $first ? $endOffset < $upperBound : false,
            ],
        ];
    }

    public static function edgeForObjectWithIndex($object, $index)
    {
        return [
            'cursor' => ArrayConnection::keyToCursor($index),
            'node'   => $object
        ];
    }

}
