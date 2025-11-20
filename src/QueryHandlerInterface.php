<?php

namespace VSHF\Bus;

/**
 * Class QueryHandlerInterface
 *
 * @package VSHF
 */
interface QueryHandlerInterface
{
    /**
     * @param QueryInterface $query
     *
     * @return mixed
     */
    public function ask(QueryInterface $query): mixed;
}
