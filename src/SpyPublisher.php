<?php
declare (strict_types=1);

namespace Quotebot;

class SpyPublisher implements PublisherInterface
{

    /**
     * @var array
     */
    private $proposals;

    public function publish($proposal): void
    {
        $this->proposals[] = $proposal;
    }

    public function proposals(): array
    {
        return $this->proposals;
    }


    public function count(): int
    {
        return count($this->proposals);
    }
}
