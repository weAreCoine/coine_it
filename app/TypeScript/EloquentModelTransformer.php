<?php

declare(strict_types=1);

namespace App\TypeScript;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Spatie\TypeScriptTransformer\Structures\MissingSymbolsCollection;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\Transformers\Transformer;

class EloquentModelTransformer implements Transformer
{
    /** @var array<string, string> */
    private const PHP_TO_TS_TYPES = [
        'int' => 'number',
        'integer' => 'number',
        'float' => 'number',
        'double' => 'number',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'string' => 'string',
        'array' => 'Array<any>',
        'mixed' => 'any',
        'null' => 'null',
    ];

    public function transform(ReflectionClass $class, string $name): ?TransformedType
    {
        if (! $class->isSubclassOf(Model::class)) {
            return null;
        }

        $properties = $this->extractPhpDocProperties($class);

        if (empty($properties)) {
            return null;
        }

        $tsProperties = array_map(
            fn (array $prop) => "{$prop['name']}{$prop['optional']}: {$prop['type']};",
            $properties,
        );

        $type = '{'.PHP_EOL.implode(PHP_EOL, $tsProperties).PHP_EOL.'}';

        return TransformedType::create(
            $class,
            $name,
            $type,
            new MissingSymbolsCollection,
        );
    }

    /**
     * @return list<array{name: string, type: string, optional: string}>
     */
    private function extractPhpDocProperties(ReflectionClass $class): array
    {
        $docComment = $class->getDocComment();

        if ($docComment === false) {
            return [];
        }

        preg_match_all(
            '/@property(?:-read)?\s+([\w|\\\\?]+)\s+\$(\w+)/',
            $docComment,
            $matches,
            PREG_SET_ORDER,
        );

        return array_map(fn (array $match) => [
            'name' => $match[2],
            'type' => $this->convertType($match[1]),
            'optional' => str_contains($match[1], 'null') ? '?' : '',
        ], $matches);
    }

    private function convertType(string $phpType): string
    {
        $types = explode('|', str_replace('?', 'null|', $phpType));

        $tsTypes = array_map(
            fn (string $type) => self::PHP_TO_TS_TYPES[trim($type)] ?? 'any',
            $types,
        );

        return implode(' | ', array_unique($tsTypes));
    }
}
