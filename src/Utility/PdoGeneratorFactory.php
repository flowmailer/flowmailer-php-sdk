<?php

declare(strict_types=1);

/*
 * This file is part of the Flowmailer PHP SDK package.
 * Copyright (c) 2021 Flowmailer BV
 */

namespace Flowmailer\API\Utility;

class PdoGeneratorFactory
{
    public function __construct(
        private readonly \PDO $pdo
    ) {
    }

    public function createGenerator($select): \Generator
    {
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $statement = $this->pdo->query($select, \PDO::FETCH_ASSOC);
        $statement->execute();

        foreach ($statement as $row) {
            yield $row;
        }
    }
}
