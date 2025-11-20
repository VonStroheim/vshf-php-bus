<?php

namespace VSHF\Bus;

/**
 * Class QueryInterface
 *
 * @template TResult
 * @package VSHF
 */
interface QueryInterface
{
    /**
     * @return class-string<TResult>
     */
    public function getResultType(): string;
}
